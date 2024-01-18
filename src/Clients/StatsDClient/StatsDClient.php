<?php

namespace AlexFN\NanoService\Clients\StatsDClient;

use AlexFN\NanoService\Clients\StatsDClient\Enums\EventStatusTag;
use AlexFN\NanoService\Clients\StatsDClient\Enums\RetryTag;
use League\StatsD\Client;

class StatsDClient
{
    private bool $canStartService;

    private Client $statsd;

    private float $start;

    private array $tags = [];

    public function __construct($config = [])
    {
        $this->canStartService = isset($config['host']) && isset($config['port']) && isset($config['namespace']);

        if ($this->canStartService) {
            $this->statsd = new Client();
            $this->statsd->configure($config);
        }
    }

    public function start(array $tags): void
    {
        if (!$this->canStartService) {
            return;
        }

        $this->tags = $tags;
        $this->start = microtime(true);
        $this->statsd->increment("event_started_count", 1, 1, $this->tags);
    }

    public function end(EventStatusTag $status, RetryTag $retry): void
    {
        if (!$this->canStartService) {
            return;
        }

        $this->addTags([
            'status' => $status->value,
            'retry' => $retry->value
        ]);
        $this->statsd->timing(
            "event_processed_duration",
            (microtime(true) - $this->start) * 1000,
            $this->tags
        );
    }

    private function addTags(array $tags): void
    {
        $this->tags = array_merge($this->tags, $tags);
    }

}
