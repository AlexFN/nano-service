<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoConsumer as NanoConsumerContract;
use AlexFN\NanoService\Contracts\NanoPublisher as NanoPublisherContract;
use AlexFN\NanoService\Contracts\NanoServiceMessage as NanoServiceMessageContract;
use Exception;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;

class NanoPublisher extends NanoServiceClass implements NanoPublisherContract
{
    const PUBLISHER_ENABLED = "AMQP_PUBLISHER_ENABLED";
    private NanoServiceMessageContract $message;

    private ?int $delay = null;

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
    public function publish(string $event): void
    {
        if ((bool) $this->getEnv(self::PUBLISHER_ENABLED) !== true) return;

        $this->message->setEvent($event);
        $this->message->set('app_id', $this->getNamespace($this->getEnv(self::MICROSERVICE_NAME)));

        if ($this->delay) {
            $this->message->set('application_headers', new AMQPTable(['x-delay' => $this->delay]));
        }

        $exchange = $this->getNamespace($this->exchange);
        $this->channel->basic_publish($this->message, $exchange, $event);

        $this->channel->close();
        $this->connection->close();
    }
}
