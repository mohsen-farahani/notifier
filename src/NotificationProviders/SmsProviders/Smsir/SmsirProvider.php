<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders\Smsir;

use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;
use Asanbar\Notifier\Traits\RestConnector;
use Illuminate\Support\Facades\Log;

class SmsirProvider extends SmsAbstract
{
    use RestConnector;

    public $send_uri;
    public $token_uri;
    protected $user_api_key;
    protected $secret_key;
    public $from;

    public function __construct()
    {
        $this->send_uri = "http://restfulsms.com/api/MessageSend";
        $this->token_uri = "http://restfulsms.com/api/Token";
        $this->user_api_key = config("notifier.sms.smsir.api_key");
        $this->secret_key = config("notifier.sms.smsir.secret_key");
        $this->from = config("notifier.sms.smsir.line_number");
    }

    public function getToken()
    {
        $request = [
            "UserApiKey" => $this->user_api_key,
            "SecretKey" => $this->secret_key,
            "System" => "Notifier",
        ];

        $response = $this->post(
            $this->token_uri,
            ["json" => $request]
        );

        $response = json_decode($response->getBody(),true);

        if(array_key_exists("TokenKey", $response)) {
            return $response["TokenKey"];
        } else {
            Log::error("Notifier: Could not get token from SMS.ir");

            return false;
        }
    }

    public function send(string $message, array $numbers, string $datetime = null)
    {
        $body = [
            "Messages" => [$message],
            "MobileNumbers" => $numbers,
            "LineNumber" => $this->from,
        ];

        if($datetime) {
            $body["SendDateTime"] = $datetime;
        }

        $headers = [
            "x-sms-ir-secure-token" => $this->getToken()
        ];

        $response = $this->post(
            $this->send_uri,
            [
                "json" => $body,
                "headers" => $headers
            ]
        );

        $response = json_decode($response->getBody()->getContents(),true);

        if($response["IsSuccessful"] == true) {
            $result["result_id"] = $response["BatchKey"];
            $result["errors"] = null;
        } else {
            $result["result_id"] = null;
            $result["errors"] = $response["Message"];
        }

        return $result;
    }
}