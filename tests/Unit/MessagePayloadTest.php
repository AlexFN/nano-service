<?php

namespace AlexFN\NanoService\Tests\Unit;

use AlexFN\NanoService\NanoServiceMessage;
use PHPUnit\Framework\TestCase;

final class MessagePayloadTest extends TestCase
{
    private $message;

    protected function setUp(): void
    {
        $this->message = new NanoServiceMessage();
    }

    public function testGetPayloadAttribute(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ]);

        $this->assertEquals('Value 2', $this->message->getPayloadAttribute('key2'));
    }

    public function testGetExistPayloadAttributeWithDefaultValue(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ]);

        $this->assertEquals('Value 2', $this->message->getPayloadAttribute('key2', 'foo'));
    }

    public function testGetMissingPayloadAttributeWithDefaultValue(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ]);

        $this->assertEquals('foo', $this->message->getPayloadAttribute('key3', 'foo'));
    }

    public function testAddPayload(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addPayload([
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ]);

        $this->assertEquals([
            'key1' => 'Value 1',
            'key2' => 'Value 2',
            'key3' => 'New value 3',
        ], $this->message->getPayload());
    }

    public function testAddAndReplacePayload(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addPayload([
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ], true);

        $this->assertEquals([
            'key1' => 'New value 1',
            'key2' => 'Value 2',
            'key3' => 'New value 3',
        ], $this->message->getPayload());
    }

    public function testAddPayloadAttribute(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addPayloadAttribute('key2', [
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ]);

        $this->assertEquals([
            'key1' => 'Value 1',
            'key2' => 'Value 2',
        ], $this->message->getPayload());
    }

    public function testAddAndReplacePayloadAttribute(): void
    {
        $this->message
            ->addPayload([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addPayloadAttribute('key2', [
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ], true);

        $this->assertEquals([
            'key1' => 'Value 1',
            'key2' => [
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ],
        ], $this->message->getPayload());
    }
}

