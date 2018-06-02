<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders\SmsIr;

use Asanbar\Notifier\Constants\SmsConfigs;
use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;
use Asanbar\Notifier\Traits\RestConnector;

class SmsIrProvider extends SmsAbstract
{
    use RestConnector;

    public function getToken()
    {
        $request = [
            "UserApiKey" => SmsConfigs::SMSIR_API_KEY,
            "SecretKey" => SmsConfigs::SMSIR_SECRET_KEY,
            "System" => "laravel_v_1_4",
        ];

        $response = $this->post(
            SmsConfigs::SMSIR_TOKEN,
            ["json" => $request]
        );

        if(array_key_exists("TokenKey", $response)) {
            return $response["TokenKey"];
        } else {
            return false;
        }
    }

    public function send(array $messages, array $numbers, string $datetime = null)
    {
        $body = [
            "Messages" => $messages,
            "MobileNumbers" => $numbers,
            "LineNumber" => SmsConfigs::SMSIR_LINE_NUMBER,
        ];

        if($datetime) {
            $body["SendDateTime"] = $datetime;
        }

        $headers = [
            "x-sms-ir-secure-token" => $this->getToken()
        ];

        $this->post(
            SmsConfigs::SMSIR_URI,
            [
                "json" => $body,
                "headers" => $headers
            ]
        );

        return true;
    }
}