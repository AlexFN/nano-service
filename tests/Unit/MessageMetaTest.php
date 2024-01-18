<?php

namespace AlexFN\NanoService\Tests\Unit;

use AlexFN\NanoService\NanoServiceMessage;
use PHPUnit\Framework\TestCase;

final class MessageMetaTest extends TestCase
{
    private $message;

    protected function setUp(): void
    {
        $this->message = new NanoServiceMessage();
    }

    public function testGetMetaAttribute(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ]);

        $this->assertEquals('Value 2', $this->message->getMetaAttribute('key2'));
    }

    public function testGetExistPayloadAttributeWithDefaultValue(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ]);

        $this->assertEquals('Value 2', $this->message->getMetaAttribute('key2', 'foo'));
    }

    public function testGetMissingPayloadAttributeWithDefaultValue(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ]);

        $this->assertEquals('foo', $this->message->getMetaAttribute('key3', 'foo'));
    }

    public function testAddMeta(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addMeta([
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ]);

        $this->assertContains([
            'key1' => 'Value 1',
            'key2' => 'Value 2',
            'key3' => 'New value 3',
        ], $this->message->getMeta());
    }

    public function testAddAndReplaceMeta(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addMeta([
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ], true);

        $this->assertContains([
            'key1' => 'New value 1',
            'key2' => 'Value 2',
            'key3' => 'New value 3',
        ], $this->message->getMeta());
    }

    public function testAddMetaAttribute(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addMetaAttribute('key2', [
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ]);

        $this->assertContains([
            'key1' => 'Value 1',
            'key2' => 'Value 2',
        ], $this->message->getMeta());
    }

    public function testAddAndReplaceMetaAttribute(): void
    {
        $this->message
            ->addMeta([
                'key1' => 'Value 1',
                'key2' => 'Value 2',
            ])
            ->addMetaAttribute('key2', [
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ], true);

        $this->assertContains([
            'key1' => 'Value 1',
            'key2' => [
                'key1' => 'New value 1',
                'key3' => 'New value 3',
            ],
        ], $this->message->getMeta());
    }
}
