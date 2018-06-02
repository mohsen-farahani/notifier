<?php

namespace Asanbar\Notifier;

use App\Http\Controllers\Controller;
use Asanbar\Notifier\Constants\Message;
use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\NotificationProviders\PushProviders\OneSignal\OneSignalProvider;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Illuminate\Http\Request;

class Notifier
{
    public static function sendPush($heading, $content, $player_ids, $extra)
    {
//        $pushProvider = PushAbstract::resolve(env("ACTIVE_PUSH_PROVIDER"));

        dispatch(new SendPushJob(new OneSignalProvider(), $heading, $content, $player_ids, $extra));

        return true;
    }
}