<?php

namespace AlexFN\NanoService\Tests\Unit;

use AlexFN\NanoService\NanoServiceMessage;
use PHPUnit\Framework\TestCase;

final class MessageStatusTest extends TestCase
{
    private $message;

    protected function setUp(): void
    {
        $this->message = new NanoServiceMessage();
    }

    public function testGetDefaultStatusCode(): void
    {
        $this->assertEquals('unknown', $this->message->getStatusCode());
    }

    public function testSetStatusCode(): void
    {
        $this->message->setStatusCode('success');

        $this->assertEquals('success', $this->message->getStatusCode());
    }

    public function testGetDefaultStatusData(): void
    {
        $this->assertEquals([], $this->message->getStatusData());
    }

    public function testSetStatusData(): void
    {
        $this->message->setStatusData([
            'key1' => 'Value 1'
        ]);

        $this->assertEquals([
            'key1' => 'Value 1'
        ], $this->message->getStatusData());
    }

    public function testSetAndReplaceStatusData(): void
    {
        $this->message->setStatusData([
            'key1' => 'Value 1',
            'key2' => 'Value 2'
        ])->setStatusData([
            'key1' => 'New value 1',
        ]);

        $this->assertEquals([
            'key1' => 'New value 1'
        ], $this->message->getStatusData());
    }
}

