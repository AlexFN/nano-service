<?php

namespace AlexFN\NanoService\Traits;

trait Environment
{
    protected function getEnv(string $param): string
    {
        //return $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV');
        return getenv($param, true) ?: getenv($param) ?: $_ENV[$param];
    }
}
