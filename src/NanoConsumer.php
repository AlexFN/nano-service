<?php

namespace AlexFN\NanoService;

use ErrorException;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class NanoConsumer extends NanoServiceClass
{

    private $function;

    public function events(string $event): NanoConsumer
    {
        $this->queue($this->getEnv(self::MICROSERVICE));
        $this->exchange($event);

        return $this;
    }

    /**
     * @throws ErrorException
     */
    public function consume(callable $function)
    {
        $this->function = $function;

        $this->channel->queue_bind($this->queue, $this->exchange);

        $this->channel->basic_consume($this->queue, $this->getEnv(self::MICROSERVICE), false, false, false, false, [$this, 'consumeCallback']);
        register_shutdown_function([$this, 'shutdown'], $this->channel, $this->connection);
        $this->channel->consume();
    }

    public function consumeCallback(AMQPMessage $message)
    {
        $newMessage = new NanoServiceMessage($message->getBody(), $message->get_properties());
        $newMessage->setDeliveryTag($message->getDeliveryTag());
        $newMessage->setChannel($message->getChannel());

        call_user_func($this->function, $newMessage);
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
