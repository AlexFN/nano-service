<?php

namespace AlexFN\NanoService;

use Exception;

class NanoPublisher extends NanoServiceClass
{
    private $message;

    public function setMessage(NanoServiceMessage $message): NanoPublisher
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function publish(string $event)
    {
        $this->message->setEvent($event);

        $this->exchange($event);
        $this->channel->basic_publish($this->message, $this->exchange);

        $this->channel->close();
        $this->connection->close();
    }
}
