<?php

namespace AlexFN\NanoService\Contracts;

interface NanoPublisher
{
    /**
     * Set message
     *
     * @param NanoServiceMessage $message
     * @return NanoPublisher
     */
    public function setMessage(NanoServiceMessage $message): self;

    /**
     * Publish in exchange
     *
     * @return mixed
     */
    public function publish(string $event): void;
}
