<?php

namespace AlexFN\NanoService\Traits;

trait Environment
{
    private $config = [];

    protected $prefix = 'AMQP_';

    protected function getEnv(string $param): string
    {
        //return $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV');

        $configParam = strtolower(substr($param, strlen($this->prefix)));

        return $this->config[$configParam] ?? getenv($param, true) ?: getenv($param) ?: $_ENV[$param];
    }
}
