<?php

namespace Asanbar\Notifier;

use App\Http\Controllers\Controller;
use Asanbar\Notifier\Constants\Message;
use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\Jobs\SendSmsJob;
use Asanbar\Notifier\NotificationProviders\PushProviders\OneSignal\OneSignalProvider;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsIr\SmsIrProvider;
use Illuminate\Http\Request;

class Notifier
{
    public static function sendPush(string $heading, string $content, array $player_ids, array $extra = null)
//    public static function sendPush()
    {
//        $heading = request()->get("heading");
//        $content = request()->get("content");
//        $player_ids = request()->get("player_ids");
//        $extra = request()->get("extra");

//        $pushProvider = PushAbstract::resolve(env("ACTIVE_PUSH_PROVIDER"));

        dispatch(new SendPushJob(new OneSignalProvider(), $heading, $content, $player_ids, $extra));

        return true;
    }

    public static function sendSms(array $messages, array $numbers, string $datetime = null)
//    public static function sendSms()
    {
//        $messages = request()->get("messages");
//        $numbers = request()->get("numbers");
//        $datetime = request()->get("datetime");

        dispatch(new SendSmsJob(new SmsIrProvider(), $messages, $numbers, $datetime));

        return true;
    }
}