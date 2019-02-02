<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders\Smsir;

use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders\SmsAbstract;
use Asanbar\Notifier\Traits\RestConnector;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SmsirProvider extends SmsAbstract
{
    use RestConnector;

    public $from;
    private $sendURI;
    private $tokenURI;
    private $userApiKey;
    private $secretKey;

    public function __construct()
    {
        $this->sendURI    = "http://restfulsms.com/api/MessageSend";
        $this->receiveURI = "https://restfulsms.com/api/ReceiveMessage";
        $this->tokenURI   = "http://restfulsms.com/api/Token";
        $this->userApiKey = config("notifier.sms.smsir.api_key");
        $this->secretKey  = config("notifier.sms.smsir.secret_key");
        $this->from       = config("notifier.sms.smsir.line_number");
    }

    /**
     * @param string $message
     * @param array $numbers
     * @param Carbon|null $expireAt
     * @return array
     */
    public function send(string $message, array $numbers, ?Carbon $expireAt = null): array
    {
        $body = [
            "Messages"      => [$message],
            "MobileNumbers" => $numbers,
            "LineNumber"    => $this->from,
        ];

        //TODO: fix it
        /* if ($datetime) {
        $body["SendDateTime"] = $datetime;
        } */

        $headers = [
            "x-sms-ir-secure-token" => $this->getToken(),
        ];

        $response = $this->post(
            $this->sendURI,
            [
                "json"    => $body,
                "headers" => $headers,
            ]
        );

        $response = json_decode($response->getBody()
                ->getContents(), true);

        $result = [];
        if (isset($response["IsSuccessful"]) && $response["IsSuccessful"]) {
            $result = [
                'all_success'   => (count($numbers) == count($response['Ids'])),
                'success_count' => count($response['Ids']),
            ];

            foreach ($response['Ids'] as $mobile) {
                $result['detail']['0' . $mobile['MobileNo']] = [
                    'success'  => true,
                    'response' => $mobile['ID'],
                    'provider' => 'smsir',
                ];
            }

            $result["result_id"] = $response["BatchKey"];
            $result["errors"]    = null;
        } else {
            $result = [
                'all_success'   => false,
                'success_count' => 0,
            ];

            foreach ($numbers as $mobile) {
                $result['detail'][$mobile] = [
                    'success'  => false,
                    'response' => $response["Message"],
                    'provider' => 'smsir',
                ];
            }
        }

        if (count($numbers) != count($response['Ids'])) {
            foreach ($numbers as $mobile) {
                if (!isset($result['detail'][$mobile])) {
                    $result['detail'][$mobile] = [
                        'success'  => false,
                        'response' => null,
                        'provider' => 'smsir',
                    ];
                }
            }
        }

        return $result;
    }

    public function receiveMessages(): array
    {
        $response = $this->get(
            $this->receiveURI,
            [
                'Shamsi_FromDate'     => '1397/10/01',
                'Shamsi_ToDate'       => date('Y/m/d'),
                'RowsPerPage'         => 100,
                'RequestedPageNumber' => 1,
            ],
            [
                "headers" => [
                    "x-sms-ir-secure-token" => $this->getToken(),
                ],
            ]
        );
        $result = json_decode($response->getBody()
                ->getContents(), true);

        $finalResult = [
            'total'    => $result['CountOfAll'],
            'status'   => $result['IsSuccessful'],
            'message'  => $result['Message'],
            'messages' => [],
        ];

        foreach ($result['Messages'] as $message) {
            $finalResult['messages'][] = [
                'id'                => $message['ID'],
                'sender_identifier' => '0' . $message['MobileNo'],
                'body'              => $message['SMSMessageBody'],
                'send_at'           => Carbon::parse($message['LatinReceiveDateTime'])->format('Y-m-d H:i:s'),
            ];

            $finalResult['identifiers'][] = '0' . $message['MobileNo'];
        }

        return $finalResult;
    }

    /**
     * get token from provider function
     *
     * @return string|null
     */
    private function getToken(): ?string
    {
        $request = [
            "UserApiKey" => $this->userApiKey,
            "SecretKey"  => $this->secretKey,
            "System"     => "Notifier",
        ];

        $response = $this->post(
            $this->tokenURI,
            ["json" => $request]
        );

        $response = json_decode($response->getBody(), true);

        if (array_key_exists("TokenKey", $response)) {
            return $response["TokenKey"];
        }

        Log::error("Notifier: Could not get token from SMS.ir");
        return null;
    }

}
