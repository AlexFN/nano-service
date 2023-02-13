<?php

namespace AlexFN\NanoService\Contracts;

interface NanoServiceMessage
{
    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @param $attribute
     * @param $default
     * @return mixed
     */
    public function getPayloadAttribute($attribute, $default = null);

    /**
     * @param array $payload
     * @param bool $replace
     * @return NanoServiceMessage
     */
    public function addPayload(array $payload, bool $replace = false): self;

    /**
     * @param string $attribute
     * @param array $data
     * @param bool $replace
     * @return NanoServiceMessage
     */
    public function addPayloadAttribute(string $attribute, array $data, bool $replace = false): self;

    /**
     * @return string
     */
    public function getStatusCode(): string;

    /**
     * @param string $code
     * @return NanoServiceMessage
     */
    public function setStatusCode(string $code): self;

    /**
     * @return array
     */
    public function getStatusData(): array;

    /**
     * @param array $data
     * @return NanoServiceMessage
     */
    public function setStatusData(array $data): self;

    /**
     * @return array
     */
    public function getMeta(): array;

    /**
     * @param string $attribute
     * @param $default
     * @return mixed
     */
    public function getMetaAttribute(string $attribute, $default = null);

    /**
     * @param array $payload
     * @param bool $replace
     * @return NanoServiceMessage
     */
    public function addMeta(array $payload, bool $replace = false): self;

    /**
     * @param string $attribute
     * @param array $data
     * @param bool $replace
     * @return NanoServiceMessage
     */
    public function addMetaAttribute(string $attribute, array $data, bool $replace = false): self;

    /**
     * @return bool
     */
    public function getDebug(): bool;

    /**
     * @param bool $debug
     * @return NanoServiceMessage
     */
    public function setDebug(bool $debug = true): self;

    /**
     * @param string $event
     * @return NanoServiceMessage
     */
    public function setEvent(string $event): self;
}
