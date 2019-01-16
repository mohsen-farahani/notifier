<?php

namespace Asanbar\Notifier;

use Asanbar\Notifier\Jobs\SendMessageJob;
use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\Jobs\SendSmsJob;
use Asanbar\Notifier\NotificationService\NotifyService;
use Carbon\Carbon;

class Notifier
{
    /** @var Carbon expire at */
    private static $expireAt = null;

    /** @var string queue name */
    private static $queueName = null;

    /**
     * static set queue name function
     *
     * @param string $queueName
     * @return void
     */
    public static function onQueue(string $queueName)
    {
        self::$queueName = $queueName;
    }

    /**
     * static send push function
     *
     * @param string $title
     * @param string $description
     * @param array $receivers
     * @param array $extraData
     * @return void
     */
    public static function sendPush(string $title, string $description, array $receivers, array $extraData = null)
    {
        self::saveLog('push', $receivers, $description, $title);

        if (
            self::$queueName === null
            &&
            (self::$expireAt === null || !Carbon::now()->gt(self::$expireAt))
        ) { //if expireAt is zero or now not greater than expireAt
            $notifier = app(NotifyService::class);

            $result = $notifier->setTitle($title)
                ->setBody($description)
                ->recievers($receivers)
                ->setExpireAt(self::$expireAt)
                ->sendNotification('push');

            self::updateLog('sms', $result);

            return $result;
        }

        return dispatch((new SendPushJob($title, $description, $receivers, $extraData, self::$expireAt))->onQueue(self::$queueName));
    }

    /**
     * static send sms function
     *
     * @param string $message
     * @param array $numbers
     * @return void
     */
    public static function sendSMS(string $message, array $numbers)
    {
        self::saveLog('sms', $numbers, $message);

        if (
            self::$queueName === null
            && (self::$expireAt === null || !Carbon::now()->gt(self::$expireAt))
        ) { //if expireAt is zero or now not greater than expireAt
            $notifier = app(NotifyService::class);

            $result = $notifier->setBody($description)
                ->recievers($numbers)
                ->setExpireAt(self::$expireAt)
                ->sendNotification('sms');

            self::updateLog('sms', $result);

            return $result;

        }

        dispatch((new SendSmsJob($message, $numbers, self::$expireAt))->onQueue(self::$queueName));

        return true;
    }

    public static function sendMessage(string $title, string $body, array $user_ids)
    {
        //TODO: it should compatible with new structure ...
        dispatch((new SendMessageJob($title, $body, $user_ids, self::$expireAt))->onQueue('message'));

        return true;
    }

    /**
     * static setr expire at function
     *
     * @param Carbon $expireAt
     * @return void
     */
    public static function setExpireAt(Carbon $expireAt)
    {
        self::$expireAt = $expireAt;
    }

    /**
     * save notification log function
     *
     * @param string $type
     * @param array $recievers
     * @param string $body
     * @param string|null $title
     * @return boolean
     */
    private static function saveLog(string $type, array $recievers, string $body, ?string $title = null): bool
    {
        foreach ($recievers as $identifier) {
            $data[] = [
                'identifier' => $identifier,
                'title'      => $title,
                'body'       => trim($body),
                'type'       => ($type == 'sms' ? 0 : 1),
                'expire_at'  => self::$expireAt,
                'queued_at'  => date('Y-m-d H:i:s'),
                'try'        => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        return \DB::table('notifications')->insert($data);
    }

    /**
     * update notification log function
     *
     * @param string $type
     * @param array $result
     * @return void
     */
    private static function updateLog(string $type, array $result)
    {
        $notifications = Notification::whereIn('identifier', $this->numbers)
            ->where('body', trim($this->message))
            ->where('type', ($type == 'sms' ? 0 : 1))
            ->get();

        foreach ($notifications as $notification) {
            $sendResult = $result['detail'][$notification->identifier];

            $notification->provider_name = $sendResult['provider'];
            $notification->success_at    = ($sendResult['success'] ? date('Y-m-d H:i:s') : null);
            $notification->try++;
            $notification->error = (!$sendResult['success'] ? $sendResult['response'] : null);
            $notification->update();
        }
    }
}
