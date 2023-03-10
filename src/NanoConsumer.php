<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoConsumer as NanoConsumerContract;
use ErrorException;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class NanoConsumer extends NanoServiceClass implements NanoConsumerContract
{
    private $callback;

    private $debugCallback;

    public function events(string ...$events): NanoConsumerContract
    {
        $this->queue($this->getEnv(self::MICROSERVICE));

        foreach ($events as $event) {
            $this->exchange($event);
            $this->channel->queue_bind($this->queue, $this->exchange);
        }

        return $this;
    }

    /**
     * @throws ErrorException
     */
    public function consume(callable $callback, ?callable $debugCallback = null): void
    {
        $this->callback = $callback;
        $this->debugCallback = $debugCallback;

        $this->channel->basic_consume($this->queue, $this->getEnv(self::MICROSERVICE), false, false, false, false, [$this, 'consumeCallback']);
        register_shutdown_function([$this, 'shutdown'], $this->channel, $this->connection);
        $this->channel->consume();
    }

    public function consumeCallback(AMQPMessage $message)
    {
        $newMessage = new NanoServiceMessage($message->getBody(), $message->get_properties());
        $newMessage->setDeliveryTag($message->getDeliveryTag());
        $newMessage->setChannel($message->getChannel());

        $callback = $newMessage->getDebug() && is_callable($this->debugCallback) ? $this->debugCallback : $this->callback;

        call_user_func($callback, $newMessage);
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
