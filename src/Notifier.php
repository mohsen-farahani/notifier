<?php

namespace Asanbar\Notifier;

use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\Jobs\SendSmsJob;
use Asanbar\Notifier\Models\Push;
use Asanbar\Notifier\Models\Sms;
use Illuminate\Http\Request;

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

    public static function getPushes($player_ids, string $from_datetime = null, string $to_datetime = null,
                                     $extra_field = null, array $extra_values = null)
    {
        return Push::getPushes(
            $player_ids,
            $from_datetime,
            $to_datetime,
            $extra_field,
            $extra_values
        );
    }

    public static function getSmses(array $numbers)
    {
        return Sms::getSmses($numbers);
    }
}