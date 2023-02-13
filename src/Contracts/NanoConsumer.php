<?php

namespace AlexFN\NanoService\Contracts;

interface NanoConsumer
{
    /**
     * Register consume to queues
     *
     * @param string ...$events
     * @return NanoConsumer
     */
    public function events(string ...$events): self;

    /**
     * Consume from queues
     *
     * @param callable $callback
     * @param callable|null $debugCallback
     * @return void
     */
    public function consume(callable $callback, ?callable $debugCallback): void;
}
