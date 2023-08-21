<?php

namespace AlexFN\NanoService\Enums;

enum NanoServiceMessageStatuses: string
{
    case UNKNOWN = 'unknown';
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
    case DEBUG = 'debug';

    public function isStatusSuccess(): bool
    {
        return $this === self::SUCCESS;
    }
}
