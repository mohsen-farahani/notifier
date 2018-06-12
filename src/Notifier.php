<?php

namespace Asanbar\Notifier;

use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\Jobs\SendSmsJob;

class Notifier
{
//    public static function sendPush(string $heading, string $content, array $player_ids, array $extra = null)
    public static function sendPush()
    {
        $heading = request()->get("heading");
        $content = request()->get("content");
        $player_ids = request()->get("player_ids");
        $extra = request()->get("extra");

        dispatch(new SendPushJob($heading, $content, $player_ids, $extra));

        return true;
    }

//    public static function sendSms(string $message, array $numbers, string $datetime = null)
    public static function sendSms()
    {
        $message = request()->get("message");
        $numbers = request()->get("numbers");
        $datetime = request()->get("datetime");

        dispatch(new SendSmsJob($message, $numbers, $datetime));

        return true;
    }
}