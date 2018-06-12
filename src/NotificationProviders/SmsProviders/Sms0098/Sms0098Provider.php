<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders\Sms0098;

use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;
use Asanbar\Notifier\Traits\RestConnector;

class Sms0098Provider extends SmsAbstract
{
    use RestConnector;

    public $send_uri = "http://www.0098sms.com/sendsmslink.aspx";
    public $domain = "0098";

    public function send(string $message, array $numbers, string $datetime = null)
    {
        $query = [
            "FROM" => config("notifier.sms.sms0098.from"),
            "TO" => reset($numbers),
            "TEXT" => $message,
            "USERNAME" => config("notifier.sms.sms0098.username"),
            "PASSWORD" => config("notifier.sms.sms0098.password"),
            "DOMAIN" => $this->domain,
        ];

        $response = $this->get(
            $this->send_uri,
            $query
        );

        $response = substr(trim($response->getBody()->getContents()),0,1);

        if($response == 0) {
            return true;
        } else {
            return false;
        }
    }
}