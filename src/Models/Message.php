<?php

namespace Asanbar\Notifier\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "notifier_messages";

    protected $fillable = [
        "provider",
        "user_id",
        "title",
        "body",
        "result_id",
        "status",
        "description",
    ];

    protected $casts = [
        "description" => "array"
    ];

    protected $hidden = [
        "provider",
        "result_id",
        "description",
        "updated_at",
    ];

    const STATUS_SENT = "sent";
    const STATUS_SEND_FAILED = "send-failed";
    const STATUS_SEEN = "seen";

    public static function createMessages(string $provider, array $user_ids, string $title, string $body,
                                          string $result_id = null, string $status = null, string $description = null)
    {
        $messages = [];

        foreach($user_ids as $user_id) {
            $messages[] = [
                "provider" => $provider,
                "user_id" => $user_id,
                "title" => $title,
                "body" => $body,
                "result_id" => $result_id,
                "status" => $status,
                "description" => json_encode($description),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
        }

        self::insert($messages);
    }

    public static function createSentMessages(string $provider, array $user_ids, string $title, string $body,
                                              string $result_id)
    {
        self::createMessages(
            $provider,
            $user_ids,
            $title,
            $body,
            $result_id,
            self::STATUS_SENT
        );
    }

    public static function createSendFailedMessages(string $provider, array $user_ids, string $title, string $body,
                                                    array $description)
    {
        self::createMessages(
            $provider,
            $user_ids,
            $title,
            $body,
            null,
            self::STATUS_SEND_FAILED,
            $description
        );
    }

    public static function updateSeenMessage($message_id)
    {
        self::where("id", $message_id)
            ->update([
                "status" => self::STATUS_SEEN,
            ]);
    }

    public static function updateSeenMessages($message_ids)
    {
        self::whereIn("id", $message_ids)
            ->update([
                "status" => self::STATUS_SEEN,
            ]);
    }

    public static function getMessages($user_ids, $status = null)
    {
        $query = self::whereIn("user_id", $user_ids);

        if($status) {
            $query->where("status", $status);
        }

        return $query;
    }

    public static function getSeenMessages($user_ids)
    {
        return self::whereIn("user_id", $user_ids)
            ->where("status", self::STATUS_SEEN);
    }

    public static function getSentMessages($user_ids)
    {
        return self::whereIn("user_id", $user_ids)
            ->where("status", self::STATUS_SENT);
    }
}