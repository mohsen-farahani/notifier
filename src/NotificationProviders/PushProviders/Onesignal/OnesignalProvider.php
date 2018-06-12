<?php

namespace Asanbar\Notifier\NotificationProviders\PushProviders\Onesignal;

use Asanbar\Notifier\Constants\PushConfigs;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Asanbar\Notifier\Traits\RestConnector;
use Illuminate\Support\Facades\DB;

class OnesignalProvider extends PushAbstract
{
    use RestConnector;

    public $send_uri = "https://onesignal.com/api/v1/notifications";

    /**
     * Implementing send push notification
     *
     * @param string $heading
     * @param string $content
     * @param array $player_ids
     * @param null $extra
     * @return bool
     */
    public function send(string $heading, string $content, array $player_ids, $extra = null)
    {
        $request = [
            "app_id" => config("notifier.push.onesignal.app_id"),
            "extra" => $extra,
            "headings" => ["en" => $heading],
            "contents" => ["en" => $content]
        ];

        $headers = [
            "Content-Type" => "application/json; charset=utf-8",
            "Authorization" => "Basic " . config("notifier.push.onesignal.authorization")
        ];

        $player_ids_chunks = array_chunk($player_ids, 2000);

        foreach($player_ids_chunks as $player_ids) {
            $request["include_player_ids"] = $player_ids;

            $response = $this->post(
                $this->send_uri,
                [
                    "headers" => $headers,
                    "body" => json_encode($request)
                ]
            );

            $response = json_decode($response->getBody()->getContents(), true);

            $this->updateUserDevicesIfTokensExpired($response);
        }

        return true;
    }

    /**
     * In send push response, if some tokens have been expired, then they should be updated as logout in database
     *
     * @param array $response
     */
    protected function updateUserDevicesIfTokensExpired(array $response)
    {
        if(array_key_exists("errors", $response)) {
            $errors = $response["errors"];

            if(array_key_exists("invalid_player_ids", $errors)) {
                $invalid_player_ids = $errors["invalid_player_ids"];

                DB::table("users_devices")
                    ->whereIn("one_signal_token", $invalid_player_ids)
                    ->update(["logout" => 1]);
            }
        }
    }
}