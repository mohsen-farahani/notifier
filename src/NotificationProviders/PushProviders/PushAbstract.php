<?php

namespace Asanbar\Notifier\NotificationProviders\PushProviders;

use Asanbar\Notifier\NotificationProviders\PushProviders\OneSignal\OneSignalProvider;
use Illuminate\Support\Facades\Log;

abstract class PushAbstract implements PushInterface
{
    abstract function send(string $heading, string $content, array $player_ids, $extra = null);

    public static final function resolve($provider)
    {
        switch($provider) {
            case "onesignal":
                return new OneSignalProvider();
            default:
                Log::error("Notifier: Invalid push provider: " . strtoupper($provider));

                return false;
        }
    }
}