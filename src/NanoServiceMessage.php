<?php

namespace AlexFN\NanoService;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class NanoServiceMessage extends AMQPMessage
{
    public function __construct($data = [], $properties = [])
    {
        $body = is_array($data) ? json_encode(array_merge($this->dataStructure(), $data)) : $data;

        $properties = array_merge($this->defaultProperty(), $properties);

        parent::__construct($body, $properties);
    }

    protected function defaultProperty(): array
    {
        return [
            'message_id' => Uuid::uuid4(),
        ];
    }

    protected function dataStructure(): array
    {
        return [
            'meta' => [],
            'status' => [
                'code' => 'unknown',
                'data' => [],
            ],
            'payload' => [],
        ];
    }

    // Body setters/getters

    protected function addData(string $key, array $data, $replace = false)
    {
        $bodyData = json_decode($this->getBody(), true);
        $result = array_replace_recursive($bodyData, [
            $key => $data,
        ]);

        if (! $replace) {
            $result = array_replace_recursive($result, $bodyData);
        }

        $this->setBody(json_encode($result));
    }

    protected function setDataAttribute(string $attribute, string $key, $data)
    {
        $bodyData = $this->getData();
        $bodyData[$attribute][$key] = $data;

        $this->setBody(json_encode($bodyData));
    }

    protected function getData()
    {
        return json_decode($this->getBody(), true);
    }

    protected function getDataAttribute($attribute, $default = []): array
    {
        $data = $this->getData();

        return $data[$attribute] ?? $default;
    }

    /*
     * Public methods
     */

    // Payload

    public function addPayload(array $payload, $replace = false): NanoServiceMessage
    {
        $this->addData('payload', $payload, $replace);

        return $this;
    }

    public function addPayloadAttribute(string $attribute, array $data, $replace = false): NanoServiceMessage
    {
        $this->addData('payload', [
            $attribute => $data,
        ], $replace);

        return $this;
    }

    public function getPayload(): array
    {
        return $this->getDataAttribute('payload');
    }

    public function getPayloadAttribute($attribute, $default = null)
    {
        $payload = $this->getPayload();

        return $payload[$attribute] ?? $default;
    }

    // Status

    public function getStatusCode(): string
    {
        $statusData = $this->getDataAttribute('status');

        return $statusData['code'] ?? '';
    }

    public function setStatusCode(string $code): NanoServiceMessage
    {
        $this->setDataAttribute('status', 'code', $code);

        return $this;
    }

    public function getStatusData(): array
    {
        $statusData = $this->getDataAttribute('status');

        return $statusData['data'] ?? [];
    }

    public function setStatusData(array $data): NanoServiceMessage
    {
        $this->setDataAttribute('status', 'data', $data);

        return $this;
    }

    // Meta

    public function addMeta(array $payload, $replace = false): NanoServiceMessage
    {
        $this->addData('meta', $payload, $replace);

        return $this;
    }

    public function addMetaAttribute(string $attribute, array $data, $replace = false): NanoServiceMessage
    {
        $this->addData('meta', [
            $attribute => $data,
        ], $replace);

        return $this;
    }

    public function getMeta(): array
    {
        return $this->getDataAttribute('meta');
    }

    public function getMetaAttribute($attribute, $default = null)
    {
        $meta = $this->getMeta();

        return $meta[$attribute] ?? $default;
    }

    // Event property

    public function setEvent(string $event)
    {
        $this->set('type', $event);
    }
}
