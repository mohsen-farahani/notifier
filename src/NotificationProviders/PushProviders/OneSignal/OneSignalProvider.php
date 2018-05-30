<?php

namespace Asanbar\Notifier\NotificationProviders\PushProviders\OneSignal;

use Asanbar\Notifier\Constants\PushConfigs;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Asanbar\Notifier\Traits\RestConnector;

class OneSignalProvider extends PushAbstract
{
    use RestConnector;

    public function send(string $heading, string $content, array $player_ids, $extra = null)
    {
        $request = [
            "app_id" => PushConfigs::ONESIGNAL_APP_ID,
            "include_player_ids" => $player_ids,
            "extra" => $extra,
            "headings" => ["en" => $heading],
            "contents" => ["en" => $content]
        ];

        $headers = [
            "Content-Type" => "application/json; charset=utf-8",
            "Authorization" => "Basic " . PushConfigs::ONESIGNAL_AUTHORIZATION
        ];

        $response = $this->post(
            PushConfigs::ONESIGNAL_URI,
            $headers,
            $request
        );

        return $response;
    }
}