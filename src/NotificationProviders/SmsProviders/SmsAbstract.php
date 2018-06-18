<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders;

abstract class SmsAbstract implements SmsInterface
{
    public $from;

    abstract function send(string $message, array $numbers, string $datetime = null);

    public static final function resolve(string $provider)
    {
        $provider_class = sprintf("%s%s%s%s%s",
            "Asanbar\\Notifier\\NotificationProviders\\SmsProviders\\",
            ucwords($provider),
            "\\",
            ucwords($provider),
            "Provider"
        );

        return new $provider_class;
    }
}