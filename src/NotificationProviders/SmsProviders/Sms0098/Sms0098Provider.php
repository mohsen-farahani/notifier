<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders\Sms0098;

use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;
use Asanbar\Notifier\Traits\RestConnector;

class Sms0098Provider extends SmsAbstract
{
    use RestConnector;

    public $send_uri = "http://www.0098sms.com/sendsmslink.aspx";
    public $domain = "0098";
    public $from;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->from = config("notifier.sms.sms0098.from");
        $this->username = config("notifier.sms.sms0098.username");
        $this->password = config("notifier.sms.sms0098.password");
    }

    public function send(string $message, array $numbers, string $datetime = null)
    {
        $query = [
            "FROM" => $this->from,
            "TO" => reset($numbers),
            "TEXT" => $message,
            "USERNAME" => $this->username,
            "PASSWORD" => $this->password,
            "DOMAIN" => $this->domain,
        ];

        $response = $this->get(
            $this->send_uri,
            $query
        );

        if(substr(trim($response->getBody()->getContents()),0,1) == 0) {
            $result["result_id"] = 0;
            $result["errors"] = null;
        } else {
            $result["result_id"] = null;
            $result["errors"] = [substr(trim($response->getBody()->getContents()),0,2)];
        }

        return $result;
    }
}