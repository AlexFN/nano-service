<?php

namespace AlexFN\NanoService\Clients\StatsDClient\Enums;

enum EventStatusTag: string
{
    case SUCCESS = 'success';

    case FAILED = 'failed';
}
