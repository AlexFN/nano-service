<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoPublisher as NanoPublisherContract;
use AlexFN\NanoService\Contracts\NanoServiceMessage as NanoServiceMessageContract;
use Exception;
use PhpAmqpLib\Wire\AMQPTable;

class NanoPublisher extends NanoServiceClass implements NanoPublisherContract
{
    const PUBLISHER_ENABLED = 'AMQP_PUBLISHER_ENABLED';

    private NanoServiceMessageContract $message;

    private ?int $delay = null;

    private ?string $product = null;

    private ?string $env = null;

    private ?string $tenant = null;

    // Set tenant credentials
    public function setTenant(string $product, string $env, string $tenant): NanoPublisherContract
    {
        $this->product = $product;
        $this->env = $env;
        $this->tenant = $tenant;

        return $this;
    }

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
        if ((bool) $this->getEnv(self::PUBLISHER_ENABLED) !== true) {
            return;
        }

        $this->message->setEvent($event);
        $this->message->set('app_id', $this->getNamespace($this->getEnv(self::MICROSERVICE_NAME)));

        if ($this->delay) {
            $this->message->set('application_headers', new AMQPTable(['x-delay' => $this->delay]));
        }

        if ($this->product && $this->env && $this->tenant) {
            $this->message->addMeta([
                'product' => $this->product,
                'env' => $this->env,
                'tenant' => $this->tenant,
            ]);
        }

        $exchange = $this->getNamespace($this->exchange);
        $this->channel->basic_publish($this->message, $exchange, $event);

        $this->channel->close();
        $this->connection->close();
    }
}
