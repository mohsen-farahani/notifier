<?php

namespace Asanbar\Notifier;

use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\Jobs\SendSmsJob;

class Notifier
{
    public static function sendPush(string $heading, string $content, array $player_ids, array $extra = null)
    {
        dispatch(new SendPushJob($heading, $content, $player_ids, $extra));

        return true;
    }

    public static function sendSms(string $message, array $numbers)
    {
        dispatch(new SendSmsJob($message, $numbers));

        return true;
    }
}