<?php

namespace AlexFN\NanoService;

use Exception;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class NanoServiceClass
{
    const PROJECT = 'AMQP_PROJECT';

    const HOST = 'AMQP_HOST';

    const PORT = 'AMQP_PORT';

    const USER = 'AMQP_USER';

    const PASS = 'AMQP_PASS';

    const VHOST = 'AMQP_VHOST';

    const MICROSERVICE = 'AMQP_MICROSERVICE';

    //protected AMQPStreamConnection $connection;
    protected $connection;

    //protected AbstractChannel $channel;
    protected $channel;

    //protected string $exchange = 'default';
    protected $exchange = 'default';

    //protected string $queue = 'default';
    protected $queue = 'default';

    /**
     * @throws Exception
     */
    public function __construct(string $host = null, string $port = null, string $user = null, string $pass = null, string $vhost = null)
    {
        $this->connection = new AMQPStreamConnection(
            $host ?? $this->getEnv(self::HOST),
            $port ?? $this->getEnv(self::PORT),
            $user ?? $this->getEnv(self::USER),
            $pass ?? $this->getEnv(self::PASS),
            $vhost ?? $this->getEnv(self::VHOST)
        );
        $this->channel = $this->connection->channel();
    }

    protected function exchange(
        string $exchange,
        string $exchangeType = AMQPExchangeType::FANOUT,
        bool $passive = false,
        bool $durable = true,
        bool $auto_delete = false
    ): NanoServiceClass {
        $this->exchange = $this->getNamespace($exchange);
        $this->channel->exchange_declare($this->exchange, $exchangeType, $passive, $durable, $auto_delete);

        return $this;
    }

    protected function queue(string $queue, $passive = false, $durable = true, $exclusive = false, $auto_delete = false): NanoServiceClass
    {
        $this->queue = $this->getNamespace($queue);
        $this->channel->queue_declare($this->queue, $passive, $durable, $exclusive, $auto_delete);

        return $this;
    }

    protected function declare($queue): NanoServiceClass
    {
        $queue = $this->getNamespace($queue);

        $this->exchange = $queue;
        $this->queue = $queue;

        $this->exchange($queue);
        $this->queue($queue);

        return $this;
    }

    public function getProject(): string
    {
        return $this->getEnv(self::PROJECT);
    }

    public function getNamespace(string $path): string
    {
        return "{$this->getProject()}.$path";
    }

    protected function getEnv(string $param): string
    {
        return getenv($param, true) ?: getenv($param) ?: $_ENV[$param];
    }
}
