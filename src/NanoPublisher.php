<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoPublisher as NanoPublisherContract;
use AlexFN\NanoService\Contracts\NanoServiceMessage as NanoServiceMessageContract;
use Exception;

class NanoPublisher extends NanoServiceClass implements NanoPublisherContract
{
    private $message;

    public function setMessage(NanoServiceMessageContract $message): NanoPublisherContract
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function publish(string $event): void
    {
        $this->message->setEvent($event);

        $this->exchange($event);
        $this->channel->basic_publish($this->message, $this->exchange);

        $this->channel->close();
        $this->connection->close();
    }
}
