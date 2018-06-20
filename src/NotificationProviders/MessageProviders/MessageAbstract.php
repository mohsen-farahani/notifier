<?php

namespace Asanbar\Notifier\NotificationProviders\MessageProviders;

abstract class MessageAbstract implements MessageInterface
{
    abstract function send(string $title, string $body);

    public static final function resolve($provider)
    {
        try {
            $provider_class = sprintf("%s%s%s%s%s",
                "Asanbar\\Notifier\\NotificationProviders\\MessageProviders\\",
                ucwords($provider),
                "\\",
                ucwords($provider),
                "Provider"
            );

            return new $provider_class;
        } catch (\Exception $e) {
            return false;
        }
    }
}