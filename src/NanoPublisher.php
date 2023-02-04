<?php

namespace AlexFN\NanoService;

use Exception;

class NanoPublisher extends NanoServiceClass
{
    /**
     * TODO: проверять что exchange имеет хотябы одного подписчика!
     *
     */

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
        //        $this->declare($event);
        //        $this->channel->queue_bind($this->queue, $this->exchange);

        $this->exchange($event);

        $this->channel->basic_publish($this->message, $this->exchange);

        $this->channel->close();
        $this->connection->close();

        //TODO: logger
    }

}
