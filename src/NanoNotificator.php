<?php

namespace AlexFN\NanoService;

use AlexFN\NanoService\Contracts\NanoNotificator as NanoNotificatorContract;
use AlexFN\NanoService\Enums\NanoNotificatorErrorCodes;
use Exception;

class NanoNotificator extends NanoPublisher implements NanoNotificatorContract
{

    const EVENT_PREFIX = 'logs.notifications';
    private NanoServiceMessage $message;

    private string $billingType;

    private string $channelType;

    public function setNotificationEvent(string $billingType, string $channelType, string $messageId): NanoNotificatorContract
    {
        $this->billingType = $billingType;
        $this->channelType = $channelType;

        $this->message = new NanoServiceMessage();
        $this->message->setId($messageId);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function processed(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('processed', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function failed(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('failed', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function delivered(NanoNotificatorErrorCodes $code = null, string $debug = null): void
    {
        if ($code) {
            $code = NanoNotificatorErrorCodes::DELIVERED();
        }

        $this->sendEvent('delivered', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function rejected(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('rejected', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function spam_report(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('spam_report', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function expired(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('expired', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function open(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('open', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function click(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('click', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function unknown(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('unknown', $code, $debug);
    }

    /**
     * @throws Exception
     */
    public function deferred(NanoNotificatorErrorCodes $code, string $debug = null): void
    {
        $this->sendEvent('deferred', $code, $debug);
    }

    public function publishCallbackFailed(NanoServiceMessage $message): void
    {
        $failedEvents = $message->getPayload()['failed'] ?? null;
        if (empty($failedEvents)) {
            return;
        }

        foreach ($failedEvents as $failed) {
            if (isset($failed['event']) && $failed['event'] != '') {
                $nanoMessage = (new NanoServiceMessage([
                    'payload' => $failed['payload'] ?? [],
                    'encrypted' => $failed['encrypted'] ?? [],
                    'meta' => $failed['meta'] ?? [],
                    'system' => $failed['system'] ?? [],
                    'status' => $failed['status'] ?? [],
                ]));

                $nanoMessage->setId($message->getId());

                $this->setMessage($nanoMessage)
                    ->publish($failed['event']);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function sendEvent(string $status, NanoNotificatorErrorCodes $code, string $debug = null)
    {
        $this->message
            ->setStatus([
                'code' => $status,
                'error' => $code->getValue(),
                'debug' => $debug
            ])->addMeta([
                'billing_type' => $this->billingType,
                'channel_type' => $this->channelType,
            ]);

        $this->setMessage($this->message)->publish(self::EVENT_PREFIX . ".$this->billingType.$this->channelType");
    }
}
