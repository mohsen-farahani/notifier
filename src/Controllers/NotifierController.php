<?php

namespace Asanbar\Notifier\Controllers;

use App\Http\Controllers\Controller;
use Asanbar\Notifier\Notifier;
use Illuminate\Http\Request;

class NotifierController extends Controller
{
    public function sendPush(Request $request)
    {
        $this->validate($request, [
            "heading" => "required",
            "content" => "required",
            "player_ids" => "required|array",
            "extra" => "array",
        ]);

        Notifier::sendPush(
          $request["heading"],
          $request["content"],
          $request["player_ids"],
          $request["extra"] ?? null
        );

        return response()->json(
            "",
            204
        );
    }

    public function sendSms(Request $request)
    {
        $this->validate($request, [
            "message" => "required",
            "numbers" => "required|array"
        ]);

        Notifier::sendSms(
            $request["message"],
            $request["numbers"]
        );

        return response()->json(
            "",
            204
        );
    }
}
