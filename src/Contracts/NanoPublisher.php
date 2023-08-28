<?php

namespace AlexFN\NanoService\Contracts;

interface NanoPublisher
{
    /**
     * Set message
     *
     * @param  NanoServiceMessage  $message
     * @return NanoPublisher
     */
    public function setMessage(NanoServiceMessage $message): self;

    /**
     * Set tenant credentials
     *
     * @param  string  $product
     * @param  string  $env
     * @param  string  $tenant
     * @return NanoPublisher
     */
    public function setTenant(string $product, string $env, string $tenant): self;

    /**
     * Set delay
     *
     * @param  int  $delay
     * @return self
     */
    public function delay(int $delay): self;

    /**
     * Publish in exchange
     *
     * @return mixed
     */
    public function publish(string $event): void;
}
