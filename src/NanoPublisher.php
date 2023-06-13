<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoPublisher as NanoPublisherContract;
use AlexFN\NanoService\Contracts\NanoServiceMessage as NanoServiceMessageContract;
use Exception;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;

class NanoPublisher extends NanoServiceClass implements NanoPublisherContract
{
    private $message;

    private $delay;

    public function setMessage(NanoServiceMessageContract $message): NanoPublisherContract
    {
        $this->message = $message;

        return $this;
    }

    public function delay(int $delay): NanoPublisherContract
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function publish(string $event, ?string $sendTo = null): void
    {
        $this->message->setEvent($event);

        if ($this->delay) {
            $this->exchange($event, 'x-delayed-message', new AMQPTable(["x-delayed-type" => AMQPExchangeType::FANOUT]));
            $this->message->set('application_headers', new AMQPTable(['x-delay' => $this->delay]));
        } else {
            $this->exchange($event);
        }

        if ($sendTo) {
            $this->channel->queue_bind($sendTo, $this->exchange);
        }

        $this->channel->basic_publish($this->message, $this->exchange);

        $this->channel->close();
        $this->connection->close();
    }
}
