<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\PushProviders\Chabok;

use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\PushProviders\PushAbstract;
use Asanbar\Notifier\Traits\RestConnector;
use Carbon\Carbon;

class ChabokProvider extends PushAbstract
{
    use RestConnector;

    private $fallback = false;

    /**
     * Implementing send push notification
     *
     * @param string $title
     * @param string $content
     * @param array $tokens
     * @param array|NULL $extra
     * @param int $expireAt
     * @return array
     */
    public function send(string $title, string $content, array $tokens, ?array $extra = null, ?Carbon $expireAt = null): array
    {
        $messages = [];
        $headers  = $this->getHeaders();
        $ttl      = ($expireAt !== null ? $expireAt->diffInSeconds(Carbon::now()) : 0);

        foreach ($tokens as $key => $token) {
            $messages[$key]         = $this->getMessage($content, $title, $extra, $ttl);
            $messages[$key]["user"] = $token;
        }

        $response = $this->post(
            $this->getUri(),
            [
                'headers' => $headers,
                'body'    => json_encode($messages),
            ]
        );

        $result = [
            'all_success'   => true,
            'success_count' => 0,
        ];
        if ($response->getStatusCode() == 200) {
            $response = json_decode($response->getBody()
                    ->getContents(), true);

            foreach ($tokens as $key => $token) {
                $result['detail'][$token] = [
                    'success'  => true,
                    'id'       => $response[$key]['id'],
                    'count'    => $response[$key]['count'],
                    'response' => null,
                    'provider' => 'chabok',
                ];

                if ($response[$key]['count'] > 0) {
                    $result['success_count']++;
                } else {
                    $result['detail'][$token]['success'] = false;
                    $result['all_success']               = false;
                }
            }
        } else {
            foreach ($tokens as $key => $token) {
                $result['detail'][$token] = [
                    'success'  => false,
                    'id'       => null,
                    'count'    => 0,
                    'response' => $response->getContents(),
                    'provider' => 'chabok',
                ];
            }

            $result['all_success'] = false;
        }

        return $result;

    }

    /**
     * get needed headers function
     *
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            "Content-Type" => "application/json; charset=utf-8",
            "accept"       => "application/json",
        ];
    }

    /**
     * get message body function
     *
     * @param string $title
     * @param string $content
     * @param mixed[] $extra
     * @param integer $expireAt
     * @return array
     */
    private function getMessage(string $title, string $content, ?array $extra = null, int $expireAt = 0): array
    {
        $message = [
            "channel"      => "",
            "content"      => $content,
            "data"         => $extra,
            "notification" => [
                "title" => $title,
                "body"  => $content,
            ],
            "ttl"          => $expireAt,
        ];

        if ($this->fallback) {
            array_push($message, [
                'fallback' => [
                    'content' => $content,
                    'delay'   => 0,
                    'media'   => $this->fallback,
                ],
            ]);
        }

        return $message;
    }

    /**
     * get uri function
     *
     * @return string
     */
    private function getUri(): string
    {
        $baseUri = config('notifier.push.chabok.uri');
        if (config('app.env') !== "production") {
            $baseUri = config('notifier.push.chabok.uri_dev');
        }
        return $baseUri . "push/toUsers?access_token=" . config('notifier.push.chabok.access_token');
    }

    /**
     * @param array $options
     * @return self
     */
    public function options(array $options)
    {
        if (array_key_exists('fallback', $options)) {
            $this->fallback = $options['fallback'];
        }

        return $this;
    }
}
