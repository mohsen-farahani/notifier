<?php

namespace Asanbar\Notifier\NotificationProviders\PushProviders\Chabok;

use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Asanbar\Notifier\Traits\RestConnector;
use Illuminate\Support\Facades\Log;

class ChabokProvider extends PushAbstract
{
    use RestConnector;

    /**
     * Implementing send push notification
     *
     * @param string $heading
     * @param string $content
     * @param array $player_ids
     * @param null $extra
     * @return array
     */
    public function send(string $content, string $heading, array $player_ids, $extra = null) : array
    {

        $message = $this->getMessage($content, $heading, $extra);
        $headers = $this->getHeaders();

        $uri = $this->getUri();
        
        $message["user"] = $player_ids;

        try {
            $response = $this->post(
                    $uri,
                    [
                        'headers' => $headers,
                        'body' => json_encode($message)
                    ]
                );

            $response = json_decode($response->getBody()->getContents(), true);
            
        } catch (Exception $exception) {
            Log::debug('chabok.error', $exception->getTrace());
        }


        $result["result_id"] = $response["id"] ?? null;
        $result["errors"] = $response["errors"] ?? null;

        return $result;
    }


    private function getMessage(string $content, string $heading, $extra = null) : array
    {
        return [
            "channel" => "",
            "content" => $content,
            "data" => $extra,
            "notification" => [
                "title" => $heading,
                "body" => $content
            ]
        ];
    }

    private function getHeaders() : array
    {
        return [
            "Content-Type" => "application/json; charset=utf-8",
            "accept" => "application/json",
            // "Authorization" => "Basic " . config('notifier.push.chabok.access_token')
        ];
    }

    private function getUri() : string
    {
        $baseUri = config('notifier.push.chabok.uri');
        if (config('app.env') !== "production") {
            $baseUri = config('notifier.push.chabok.uri_dev');
        }
        return $baseUri . "push/toUsers?access_token=" . config('notifier.push.chabok.access_token');
    }

}