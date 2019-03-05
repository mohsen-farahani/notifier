<?php

namespace Asanbar\Notifier;

use Asanbar\Notifier\Jobs\SendMessageJob;
use Asanbar\Notifier\Jobs\SendPushJob;
use Asanbar\Notifier\Jobs\SendSmsJob;
use Asanbar\Notifier\Models\Notification;
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
        self::saveLog('push', $receivers, $description, $title , $extraData);

        if (
            self::$queueName === null
            &&
            (self::$expireAt === null || !Carbon::now()->gt(self::$expireAt))
        ) { //if expireAt is zero or now not greater than expireAt
            $notifier = app(NotifyService::class);

            $result = $notifier->setTitle($title)
                ->setBody($description)
                ->receivers($receivers)
                ->setExpireAt(self::$expireAt)
                ->sendNotification('push');

            self::updateLog('push', $result, $receivers, '');

            return $result;
        }

        return dispatch((new SendPushJob($title, $description, $receivers, $extraData, self::$expireAt))->onQueue(self::$queueName));
    }

    /**
     * static send sms function
     *
     * @param string $message
     * @param array $receivers
     * @return mixed
     */
    public static function sendSMS(string $message, array $receivers)
    {
        self::saveLog('sms', $receivers, $message);

        if (
            self::$queueName === null
            && (self::$expireAt === null || !Carbon::now()->gt(self::$expireAt))
        ) { //if expireAt is zero or now not greater than expireAt
            $notifier = app(NotifyService::class);

            $result = $notifier->setBody($description)
                ->receivers($receivers)
                ->setExpireAt(self::$expireAt)
                ->sendNotification('sms');

            self::updateLog('sms', $result, $receivers, $message);

            return $result;

        }

        dispatch((new SendSmsJob($message, $receivers, self::$expireAt))->onQueue(self::$queueName));

        return true;
    }

    /**
     * receive sms function
     *
     * @return mixed[]
     */
    public static function receiveSMS(): array
    {
        $notifier = app(NotifyService::class);

        return $notifier->receiveNotification('sms');
    }

    /**
     * send direct message function
     *
     * @param string $title
     * @param string $body
     * @param array $receivers
     * @return mixed
     */
    public static function sendMessage(string $title, string $body, array $receivers)
    {
        self::saveLog('message', $receivers, $body, $title);

        if (self::$queueName === null
            && (self::$expireAt === null || !Carbon::now()->gt(self::$expireAt))) { //if expireAt is zero or now not greater than expireAt
            $notifier = app(NotifyService::class);

            $result = $notifier->setTitle($title)
                ->setBody($body)
                ->receivers($receivers)
                ->setExpireAt(self::$expireAt)
                ->sendNotification('message');

            self::updateLog('message', $result, $receivers, $body);

            return $result;
        }

        return dispatch((new SendMessageJob($title, $body, $receivers, self::$expireAt))->onQueue('message'));
    }

    /**
     * static setter expire at function
     *
     * @param Carbon $expireAt
     * @return void
     */
    public static function setExpireAt(Carbon $expireAt)
    {
        self::$expireAt = $expireAt;
    }

    /**
     * static function to set read notification
     *
     * @param int $id
     * @param string|integer $identifier
     * @return bool
     */
    public static function read(int $id, $identifier): bool
    {
        $notification = Notification::where('id', $id)
            ->whereNull('read_at')
            ->where(function($q) use ($identifier) {
                $q->where('user_id', $identifier);
                $q->orWhere('identifier', $identifier);
            })
            ->first();

        if (empty($notification)) {
            return false;
        }

        $notification->read_at = date('Y-m-d H:i:s');
        $notification->update();

        return true;
    }

    /**
     * static function to get read notifications
     *
     * @param mixed|null $identifier
     * @param string|null $type
     * @param int|null $limit
     * @return object
     */
    public static function getReads($identifier = null, ?string $type = null, ?int $limit = null): object
    {
        $query = Notification::whereNotNull('read_at');

        if ($identifier !== null) {
            $query = $query->where('user_id', $identifier)
                ->orWhere('identifier', $identifier);
        }

        if ($type !== null) {
            $query = $query->Where('type', $type);
        }

        if ($limit !== null) {
            $notifications = $query->paginate($limit);
        } else {
            $notifications = $query->get();
        }

        return $notifications;
    }

    /**
     * static function to get unread notifications
     *
     * @param mixed|null $identifier
     * @param string|null $type
     * @param int|null $limit
     * @return object
     */
    public static function getUnReads($identifier = null, ?string $type = null, ?int $limit = null): object
    {
        $query = Notification::whereNull('read_at');

        if ($identifier !== null) {
            $query = $query->where('user_id', $identifier)
                ->orWhere('identifier', $identifier);
        }

        if ($type !== null) {
            $query = $query->Where('type', $type);
        }

        if ($limit !== null) {
            $notifications = $query->paginate($limit);
        } else {
            $notifications = $query->get();
        }

        return $notifications;
    }

    /**
     * static function to get count of notifications base on status
     *
     * @param mixed|null $identifier
     * @return array
     */
    public static function getCounts($identifier = null): array
    {
        $query = Notification::select([
            'user_id',
            'identifier',
            'type',
            \DB::raw('COUNT(id) AS all_count'),
            \DB::raw('
                 SUM(
                    CASE
                        WHEN
                            success_at IS NOT NULL
                        THEN
                            1
                        ELSE
                            0
                    END
                ) AS success_count
            '),
            \DB::raw('
                 SUM(
                    CASE
                        WHEN
                            read_at IS NOT NULL
                        THEN
                            1
                        ELSE
                            0
                    END
                ) AS read_count
            '),
            \DB::raw('
                 SUM(
                    CASE
                        WHEN
                            error IS NOT NULL
                        THEN
                            1
                        ELSE
                            0
                    END
                ) AS failed_count
            '),
        ]);

        if ($identifier !== null) {
            $query = $query->where('user_id', $identifier)
                ->orWhere('identifier', $identifier);
        }

        $data = $query->groupBy('type')
            ->get()
            ->toArray();

        $keys = array_flip(Notification::$typesKey);
        $result = [];
        foreach ($data as $value) {
            $result[$keys[$value['type']]]                 = $value;
            $result[$keys[$value['type']]]['unread_count'] = $value['success_count'] - $value['read_count'];
        }

        return $result;
    }

    /**
     * save notification log function
     *
     * @param string $type
     * @param array $receivers
     * @param string $body
     * @param string|null $title
     * @return boolean
     */
    private static function saveLog(string $type, array $receivers, string $body, ?string $title = null , ?array $extraData = null): bool
    {

        foreach ($receivers as $identifier => $userId) {
            $data[] = [
                'user_id'    => $userId,
                'identifier' => $identifier,
                'title'      => $title,
                'body'       => trim($body),
                'extra'      => json_encode($extraData),
                'type'       => Notification::$typesKey[$type],
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
     * @param mixed[] $result
     * @param mixed[] $receivers
     * @param string $body
     * @return void
     */
    private static function updateLog(string $type, array $result, array $receivers, string $body)
    {
        $identifiers = array_keys($receivers);

        $notifications = Notification::whereIn('identifier', $identifiers)
            ->where('body', trim($body))
            ->where('type', Notification::$typesKey[$type])
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
