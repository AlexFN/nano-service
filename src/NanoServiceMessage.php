<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoServiceMessage as NanoServiceMessageContract;
use AlexFN\NanoService\Traits\Environment;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Spatie\Crypto\Rsa\Exceptions\CouldNotDecryptData;
use Spatie\Crypto\Rsa\PrivateKey;
use Spatie\Crypto\Rsa\PublicKey;

class NanoServiceMessage extends AMQPMessage implements NanoServiceMessageContract
{
    use Environment;

    const PRIVATE_KEY = 'AMQP_PRIVATE_KEY';

    const PUBLIC_KEY = 'AMQP_PUBLIC_KEY';

    private $private_key;

    private $public_key;

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
            'system' => [
                'is_debug' => false,
            ],
            'encrypted' => [],
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

    protected function getDataAttribute($attribute, $default = [])
    {
        $data = $this->getData();

        return $data[$attribute] ?? $default;
    }

    /*
     * Public methods
     */

    // Payload

    public function addPayload(array $payload, $replace = false): NanoServiceMessageContract
    {
        $this->addData('payload', $payload, $replace);

        return $this;
    }

    public function addPayloadAttribute(string $attribute, array $data, $replace = false): NanoServiceMessageContract
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

    public function setStatusCode(string $code): NanoServiceMessageContract
    {
        $this->setDataAttribute('status', 'code', $code);

        return $this;
    }

    public function getStatusData(): array
    {
        $statusData = $this->getDataAttribute('status');

        return $statusData['data'] ?? [];
    }

    public function setStatusData(array $data): NanoServiceMessageContract
    {
        $this->setDataAttribute('status', 'data', $data);

        return $this;
    }

    // Meta

    public function addMeta(array $payload, $replace = false): NanoServiceMessageContract
    {
        $this->addData('meta', $payload, $replace);

        return $this;
    }

    public function addMetaAttribute(string $attribute, array $data, $replace = false): NanoServiceMessageContract
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

    public function setEvent(string $event): NanoServiceMessageContract
    {
        $this->set('type', $event);

        return $this;
    }

    // Debug mode

    public function setDebug(bool $debug = true): NanoServiceMessageContract
    {
        $this->setDataAttribute('system', 'is_debug', $debug);

        return $this;
    }

    public function getDebug(): bool
    {
        $system = $this->getDataAttribute('system');

        return $system['is_debug'] ?? false;
    }

    // Encrypted attributes

    /**
     * @param  string  $attribute
     * @param  null  $default
     * @return string
     *
     * @throws CouldNotDecryptData
     */
    public function getEncryptedAttribute(string $attribute, $default = null): string
    {
        if (! $this->public_key) {
            $this->public_key = PublicKey::fromString($this->getEnv(self::PUBLIC_KEY));
        }

        $encryptedData = $this->getDataAttribute('encrypted', []);

        $encryptedAttribute = $encryptedData[$attribute] ?? null;

        return $encryptedAttribute ? $this->public_key->decrypt(base64_decode($encryptedAttribute)) : $default;
    }

    public function setEncryptedAttribute(string $attribute, string $value): NanoServiceMessageContract
    {
        if (true) {
            $this->private_key = PrivateKey::fromString($this->getEnv(self::PRIVATE_KEY));
        }

        $encryptedAttribute = base64_encode($this->private_key->encrypt($value));

        $this->setDataAttribute('encrypted', $attribute, $encryptedAttribute);

        return $this;
    }
}
