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
            "extra" => $extra,
            "headings" => ["en" => $heading],
            "contents" => ["en" => $content]
        ];

        $headers = [
            "Content-Type" => "application/json; charset=utf-8",
            "Authorization" => "Basic " . PushConfigs::ONESIGNAL_AUTHORIZATION
        ];

        $player_ids_chunks = array_chunk($player_ids, 2000);

        foreach($player_ids_chunks as $player_ids) {
            $request["include_player_ids"] = $player_ids;

            $this->post(
                PushConfigs::ONESIGNAL_URI,
                $headers,
                $request
            );
        }

        return true;
    }
}