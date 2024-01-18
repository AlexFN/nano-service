<?php

namespace AlexFN\NanoService\Clients\StatsDClient;

use AlexFN\NanoService\Clients\StatsDClient\Enums\StatsDStatus;
use League\StatsD\Client;

class StatsDClient
{
    private bool $canStartService;

    private Client $statsd;

    private float $start;

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

        $this->start = microtime(true);
        $this->statsd->increment("event_started_count", 1, 1, $tags);
    }

    public function end(StatsDStatus $status): void
    {
        if (!$this->canStartService) {
            return;
        }

        $this->statsd->timing(
            "event_processed_duration",
            (microtime(true) - $this->start) * 1000,
            [
                'status' => $status->value
            ]
        );
    }

}
