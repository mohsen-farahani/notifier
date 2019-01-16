<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders\Sms0098;

use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders\SmsAbstract;
use Asanbar\Notifier\Traits\RestConnector;
use Carbon\Carbon;

class Sms0098Provider extends SmsAbstract
{
    use RestConnector;

    public $from;
    private $send_uri = "http://www.0098sms.com/sendsmslink.aspx";
    private $domain   = "0098";
    private $username;
    private $password;

    public function __construct()
    {
        $this->from     = config("notifier.sms.sms0098.from");
        $this->username = config("notifier.sms.sms0098.username");
        $this->password = config("notifier.sms.sms0098.password");
    }

    /**
     * @param string $message
     * @param array $numbers
     * @param Carbon|null $expireAt
     * @return array
     */
    public function send(string $message, array $numbers, ?Carbon $expireAt = null): array
    {
        $query = [
            "FROM"     => $this->from,
            "TEXT"     => $message,
            "USERNAME" => $this->username,
            "PASSWORD" => $this->password,
            "DOMAIN"   => $this->domain,
        ];

        $result = [
            'all_success'   => true,
            'success_count' => 0,
        ];

        foreach ($numbers as $number) {
            $query['TO'] = $number;

            $response = $this->get(
                $this->send_uri,
                $query
            );

            $status = $this->isSuccess($response);

            $result['detail'][$number] = [
                'success'  => $status,
                'response' => (method_exists($response, 'getContent') ? $response->getContent() : null),
                'provider' => '0098',
            ];

            if ($status) {
                $result['success_count']++;
            } else {
                $result['all_success'] = false;
            }
        }

        return $result;
    }

    private function isSuccess($response): bool
    {
        return $response->getStatusCode() == 200
            && (
            (
                property_exists($response, 'code')
                && $response->code == 0
            )
            ||
            (
                property_exists($response, 'Code')
                && $response->Code == 0
            )
        );
    }
}
