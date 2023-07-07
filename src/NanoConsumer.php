<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoConsumer as NanoConsumerContract;
use AlexFN\NanoService\SystemHandlers\SystemPing;
use ErrorException;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class NanoConsumer extends NanoServiceClass implements NanoConsumerContract
{
    protected array $handlers = [
        'system.ping.1' => SystemPing::class
    ];
    private $callback;

    private $debugCallback;

    private array $events;

    private int $tries = 0;

    private int $ttl = 0;

    public function init(): NanoConsumerContract
    {
        if ($this->tries && $this->ttl) {
            $this->initialWithFailedQueue();
        } else {
            $this->initialQueue();
        }

        $exchange = $this->getNamespace($this->exchange);

        foreach ($this->events as $event) {
            $this->channel->queue_bind($this->queue, $exchange, $event);
        }

        // Bind system events
        foreach (array_keys($this->handlers) as $systemEvent) {
            $this->channel->queue_bind($this->queue, $exchange, $systemEvent);
        }

        return $this;
    }

    private function initialQueue() {
        $this->queue($this->getEnv(self::CONSUMER_NAME));
    }

    private function initialWithFailedQueue() {
        $queue = $this->getEnv(self::CONSUMER_NAME);
        $dlx = $this->getNamespace($queue) . '.failed';

        $this->queue($queue, new AMQPTable([
            'x-dead-letter-exchange' => $dlx
        ]));
        $this->createExchange($this->queue);

        $this->createQueue($dlx, new AMQPTable([
            'x-message-ttl' => $this->ttl,
            'x-dead-letter-exchange' => $this->queue
        ]));
        $this->createExchange($dlx);

        $this->channel->queue_bind($this->queue, $this->queue, '#');
        $this->channel->queue_bind($dlx, $dlx, '#');
    }

    public function events(string ...$events): NanoConsumerContract
    {
        $this->events = $events;

        return $this;
    }

    public function failed(int $tries, int $ttl): NanoConsumerContract
    {
        $this->tries = $tries;
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @throws ErrorException
     */
    public function consume(callable $callback, ?callable $debugCallback = null): void
    {
        $this->init();

        $this->callback = $callback;
        $this->debugCallback = $debugCallback;

        $this->channel->basic_consume($this->queue, $this->getEnv(self::CONSUMER_NAME), false, false, false, false, [$this, 'consumeCallback']);
        register_shutdown_function([$this, 'shutdown'], $this->channel, $this->connection);
        $this->channel->consume();
    }

    /**
     * @throws \Throwable
     */
    public function consumeCallback(AMQPMessage $message)
    {
        $newMessage = new NanoServiceMessage($message->getBody(), $message->get_properties());
        $newMessage->setDeliveryTag($message->getDeliveryTag());
        $newMessage->setChannel($message->getChannel());

        $key = $message->get('type');
        if (array_key_exists($key, $this->handlers)) {

            // System handler
            (new $this->handlers[$key]())($newMessage);
        } else {

            // User handler
            $callback = $newMessage->getDebug() && is_callable($this->debugCallback) ? $this->debugCallback : $this->callback;

            try {
                call_user_func($callback, $newMessage);
            } catch (\Throwable $e) {

                if ($newMessage->getRetryCount() < $this->tries) {
                    $newMessage->reject(false);
                    throw $e;
                }
            }

        }

        $newMessage->ack();
    }

    /**
     * @throws Exception
     */
    public function shutdown()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
