<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders;

use Asanbar\Notifier\NotificationProviders\SmsProviders\Sms0098\Sms0098Provider;
use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsIr\SmsIrProvider;
use Illuminate\Support\Facades\Log;

abstract class SmsAbstract implements SmsInterface
{
    abstract function send(string $message, array $numbers, string $datetime = null);

    public static final function resolve(string $provider)
    {
        switch($provider) {
            case "smsir":
                return new SmsIrProvider();
            case "sms0098":
                return new Sms0098Provider();
            default:
                Log::error("Notifier: Invalid SMS provider: " . strtoupper($provider));

                return false;
        }
    }
}