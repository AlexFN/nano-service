<?php

namespace AlexFN\NanoService\Contracts;

use AlexFN\NanoService\Enums\NanoNotificatorErrorCodes;
use AlexFN\NanoService\NanoServiceMessage;

interface NanoNotificator
{
    /**
     * Set message
     */
    public function setNotificationEvent(string $billingType, string $channelType, string $messageId): self;

    /**
     * Set tenant credentials
     */
    public function processed(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function failed(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function delivered(NanoNotificatorErrorCodes $code = null, string $debug = null): void;

    public function rejected(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function spam_report(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function expired(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function open(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function click(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function unknown(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function deferred(NanoNotificatorErrorCodes $code, string $debug = null): void;

    public function publishCallbackFailed(NanoServiceMessage $message): void;
}
