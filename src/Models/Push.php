<?php

namespace Asanbar\Notifier\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Push extends Model
{
    protected $table = "notifier_pushes";

    protected $fillable = [
        "provider",
        "player_id",
        "heading",
        "content",
        "extra",
        "result_id",
        "status",
        "description",
    ];

    const STATUS_SENT = "sent";
    const STATUS_SEND_FAILED = "send-failed";
    const STATUS_SEEN = "seen";

    public static function createPushes(string $provider, array $player_ids, string $heading,
                                      string $content, array $extra = null, string $result_id = null,
                                      string $status, array $description = null)
    {
        $pushes = [];

        foreach($player_ids as $player_id) {
            $pushes[] = [
                "provider" => $provider,
                "player_id" => $player_id,
                "heading" => $heading,
                "content" => $content,
                "extra" => json_encode($extra),
                "result_id" => $result_id,
                "status" => $status,
                "description" => json_encode($description),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
        }

        self::insert($pushes);
    }

    public static function createSentPushes(string $provider, array $player_ids, string $heading,
                                          string $content, array $extra = null, string $result_id)
    {
        self::createPushes(
            $provider,
            $player_ids,
            $heading,
            $content,
            $extra,
            $result_id,
            self::STATUS_SENT
        );
    }

    public static function createSendFailedPushes(string $provider, array $player_ids, string $heading,
                                                string $content, array $extra = null, array $description)
    {
        self::createPushes(
            $provider,
            $player_ids,
            $heading,
            $content,
            $extra,
            null,
            self::STATUS_SEND_FAILED,
            $description
        );
    }
}