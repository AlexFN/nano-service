<?php

namespace AlexFN\NanoService;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class NanoServiceMessage extends AMQPMessage
{
    public function __construct($data = [], $properties = [])
    {
        $body = is_array($data) ? json_encode($data) : $data;

        $properties = array_merge($this->setDefaultProperty(), $properties);

        parent::__construct($body, $properties);
    }

    private function setDefaultProperty(): array
    {
        return [
            'message_id' => Uuid::uuid4(),
        ];
    }

    // Body setters/getters

    public function addData(string $key, array $data, $replace = false): NanoServiceMessage
    {
        $bodyData = json_decode($this->getBody(), true);
        $result = array_replace_recursive($bodyData, [
            $key => $data
        ]);

        if (!$replace) {
            $result = array_replace_recursive($result, $bodyData);
        }

        $this->setBody(json_encode($result));

        return $this;
    }

    public function getData()
    {
        return json_decode($this->getBody(), true);
    }

    public function getDataAttribute($attribute, $default = null)
    {
        $data = $this->getData();
        return $data[$attribute] ?? $default;
    }

    public function addPayload(array $payload, $replace = false): NanoServiceMessage
    {
        $this->addData('payload', $payload, $replace);

        return $this;
    }

    public function getPayload()
    {
        return $this->getDataAttribute('payload', []);
    }

    public function getPayloadAttribute($attribute, $default = null)
    {
        $payload = $this->getPayload();
        return $payload[$attribute] ?? $default;
    }
}
