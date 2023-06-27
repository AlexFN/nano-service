<?php

namespace AlexFN\NanoService\Contracts;

interface NanoConsumer
{
    /**
     * Register consumer to queues
     *
     * @param  string  ...$events
     * @return NanoConsumer
     */
    public function events(string ...$events): self;

    /**
     * Add failed queue for consumer
     *
     * @param int $tries
     * @param int $ttl
     * @return self
     */
    public function failed(int $tries, int $ttl): self;

    /**
     * Consume from queues
     *
     * @param  callable  $callback
     * @param  callable|null  $debugCallback
     * @return void
     */
    public function consume(callable $callback, ?callable $debugCallback): void;
}
