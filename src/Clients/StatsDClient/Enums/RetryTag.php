<?php

namespace AlexFN\NanoService\Clients\StatsDClient\Enums;

enum RetryTag: string
{
    case FIRST = 'first';
    case RETRY = 'retry';
    case LAST = 'last';
}
