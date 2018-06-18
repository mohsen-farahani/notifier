<?php

namespace Asanbar\Notifier\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $table = "notifier_smses";

    protected $fillable = [
        "provider",
        "from",
        "to",
        "text",
        "result_id",
        "status",
        "description",
    ];

    const STATUS_SENT = "sent";
    const STATUS_SEND_FAILED = "send-failed";

    public static function createSmses(string $provider, string $from, array $numbers, string $message,
                                       string $result_id = null, string $status, array $description = null)
    {
        $smses = [];

        foreach($numbers as $number) {
            $smses[] = [
                "provider" => $provider,
                "from" => $from,
                "to" => $number,
                "message" => $message,
                "result_id" => $result_id,
                "status" => $status,
                "description" => json_encode($description),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
        }

        self::insert($smses);
    }

    public static function createSentSmses(string $provider, string $from, array $numbers, string $message,
                                           string $result_id)
    {
        self::createSmses(
            $provider,
            $from,
            $numbers,
            $message,
            $result_id,
            self::STATUS_SENT
        );
    }

    public static function createSendFailedSmses(string $provider, string $from, array $numbers,
                                                 string $message, array $description)
    {
        self::createSmses(
          $provider,
          $from,
          $numbers,
          $message,
          null,
          self::STATUS_SEND_FAILED,
          $description
        );
    }
}