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
     * Set delay
     *
     * @param int $delay
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
