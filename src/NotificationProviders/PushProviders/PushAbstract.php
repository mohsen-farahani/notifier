<?php

namespace Asanbar\Notifier\NotificationProviders\PushProviders;

abstract class PushAbstract implements PushInterface
{
    abstract function send(string $heading, string $content, array $player_ids, $extra = null);

    public static final function resolve($provider)
    {
        if(in_array($provider, array_keys(config("providers.push")))) {
            $provider = config("providers.push." . $provider);
            return new $provider;
        } else {
            return false;
        }
    }
}