<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders;

use Asanbar\Notifier\Models\Notification;
use Asanbar\Notifier\Notifier;
use Carbon\Carbon;
use Exception;

abstract class SmsAbstract implements SmsInterface
{
    public $from;

    /**
     * @param string $provider
     * @return bool|self
     */
    final public static function resolve(string $provider)
    {
        try {
            $provider_class = sprintf("%s%s%s%s%s",
                "Asanbar\\Notifier\\NotificationService\\Strategies\\NotificationProviders\\SmsProviders\\",
                ucwords($provider),
                "\\",
                ucwords($provider),
                "Provider"
            );

            return new $provider_class;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $message
     * @param array $numbers
     * @param Carbon|null $expireAt
     * @return array
     */
    abstract public function send(string $message, array $numbers, ?Carbon $expireAt = null): array;

    /**
     * final receive function
     *
     * @return mixed[]
     */
    final public function receive(): array
    {
        $result = $this->receiveMessages();

        if (!$result['status']) {
            return $result;
        }

        $users = Notification::select('user_id', 'identifier')
            ->whereIn('identifier', $result['identifiers'])
            ->orderBy('id', 'DESC')
            ->groupBy('identifier')
            ->limit(count($result['identifiers']))
            ->get()
            ->keyBy('identifier')
            ->toArray();

        foreach ($result['messages'] as $key => $message) {
            $userId = (isset($users[$message['sender_identifier']]) ? $users[$message['sender_identifier']]['user_id'] : null);

            $inserted = Notification::where('user_id', $userId)
                ->where('type', Notification::$typesKey['sms'])
                ->where('action_type', Notification::$actionTypesKey['receive'])
                ->where('success_at', $message['send_at'])
                ->where('body', trim($message['body']))
                ->first();

            if ($inserted) {
                $result['messages'][$key] = $inserted;
            } else {

                $notification = new Notification();

                $notification->user_id     = $userId;
                $notification->identifier  = $message['sender_identifier'];
                $notification->title       = null;
                $notification->body        = trim($message['body']);
                $notification->type        = Notification::$typesKey['sms'];
                $notification->action_type = Notification::$actionTypesKey['receive'];
                $notification->expire_at   = null;
                $notification->success_at  = $message['send_at'];
                $notification->queued_at   = date('Y-m-d H:i:s');
                $notification->try         = 0;
                $notification->created_at  = date('Y-m-d H:i:s');

                $notification->save();

                $result['messages'][$key] = $notification;
            }
        }

        return $result;
    }

    /**
     * receive sms from provider function
     *
     * @return mixed[]
     */
    abstract protected function receiveMessages(): array;
}
