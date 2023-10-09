<?php

namespace AlexFN\NanoService\Contracts;

interface NanoConsumer
{
    /**
     * Register consumer to queues
     */
    public function events(string ...$events): self;

    /**
     * Add failed queue for consumer
     */
    public function failed(int $tries, int $ttl): self;

    /**
     * Consume from queues
     */
    public function consume(callable $callback, ?callable $debugCallback): void;
}
