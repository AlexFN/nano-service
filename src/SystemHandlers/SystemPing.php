<?php

namespace AlexFN\NanoService\SystemHandlers;

use AlexFN\NanoService\NanoServiceMessage;
use AlexFN\NanoService\Traits\Environment;
use Exception;

class SystemPing
{
    use Environment;

    /**
     * @param NanoServiceMessage $message
     * @return void
     * @throws Exception
     */
    public function __invoke(NanoServiceMessage $message)
    {
        if ($url = $this->getEnv('MONITORING_HEARTBEAT')) {
            $this->sendHeartbeatRequest($url);
        }
    }

    /**
     * @param $url
     * @return void
     * @throws Exception
     */
    private function sendHeartbeatRequest($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        print_r(curl_exec($ch));

        if(curl_errno($ch)) {
            throw new Exception('Error cURL: ' . curl_error($ch));
        }

        curl_close($ch);
    }
}
