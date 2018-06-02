<?php

namespace Asanbar\Notifier;

use App\Http\Controllers\Controller;
use Asanbar\Notifier\Constants\Message;
use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\NotificationProviders\PushProviders\OneSignal\OneSignalProvider;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Illuminate\Http\Request;

class PushController extends Controller
{
    public function send(Request $request)
    {
        $this->validate($request, [
            "heading" => "required",
            "content" => "required",
            "player_ids" => "required|array",
            "extra" => "",
        ]);

//        $pushProvider = PushAbstract::resolve(env("ACTIVE_PUSH_PROVIDER"));
        $pushProvider = new OneSignalProvider();

        if (!$pushProvider) {
            return response()->json(Message::PUSH_NOT_SUPPORTED, 404);
        }

        dispatch(new SendPushJob($pushProvider, $request));

        return response()->json(Message::PUSH_REQUEST_REGISTERED, 200);
    }
}