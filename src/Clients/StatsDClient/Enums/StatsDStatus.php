<?php

namespace AlexFN\NanoService\Clients\StatsDClient\Enums;

enum StatsDStatus: string
{
    case SUCCESS = 'success';

    case FAILED = 'failed';

    case CATCH = 'catch';
}
