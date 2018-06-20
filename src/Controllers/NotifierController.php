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

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            "title" => "required",
            "body" => "required",
            "user_ids" => "required|array"
        ]);

        Notifier::sendMessage(
            $request["title"],
            $request["body"],
            $request["user_ids"]
        );

        return response()->json(
            "",
            204
        );
    }

    public function getPushes(Request $request)
    {
        $this->validate($request, [
            "player_ids" => "required|array",
            "from_datetime" => "",
            "to_datetime" => "",
            "extra_field" => "required_with:extra_values",
            "extra_values" => "required_with:extra_field|array",
        ]);

        return response()->json(
            Notifier::getPushes(
                $request["player_ids"],
                $request["from_datetime"],
                $request["to_datetime"],
                $request["extra_field"],
                $request["extra_values"]
            )->paginate(config("notifier.pagination.per_page")),
            200
        );
    }

    public function getSmses(Request $request)
    {
        $this->validate($request, [
            "numbers" => "required|array",
        ]);

        return response()->json(
            Notifier::getSmses($request["numbers"])
                ->paginate(config("notifier.pagination.per_page")),
            200
        );
    }

    public function getMessages(Request $request)
    {
        $this->validate($request, [
            "user_ids" => "required|array",
            "status" => ""
        ]);

        return response()->json(
            Notifier::getMessages($request["user_ids"], $request["status"])
                ->paginate(config("notifier.pagination.per_page")),
            200
        );
    }

    public function updateSeenMessages(Request $request)
    {
        $this->validate($request, [
            "message_ids" => "required|array",
        ]);

        return response()->json(
            Notifier::updateSeenMessages($request["message_ids"]),
            204
        );
    }
}
