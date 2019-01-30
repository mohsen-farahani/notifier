<?php

namespace Asanbar\Notifier\NotificationService\Strategies;

use Asanbar\Notifier\Models\Notification;
use Asanbar\Notifier\NotificationService\NotificationInterface;
use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\PushProviders\PushAbstract;
use Carbon\Carbon;

class PushStrategy implements NotificationInterface
{
    /** @var string provider name */
    private $providerName = null;

    /** @var string title  */
    private $title;

    /** @var string body */
    private $body;

    /** @var string[] tokens */
    private $tokens;

    /** @var string[] receivers */
    private $receivers;

    /** @var mixed[] extra data */
    private $extra;

    /** @var Carbon expire date time */
    private $expireAt;

    /**
     * static set expire at function
     *
     * @param Carbon|null $time
     * @return self
     */
    public function setExpireAt(?Carbon $time = null): NotificationInterface
    {
        $this->expireAt = $time;

        return $this;
    }

    /**
     *  set title function
     *
     * @param string $title
     * @return NotificationInterface
     */
    public function setTitle(string $title): NotificationInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * set body function
     *
     * @param string $txt
     * @return NotificationInterface
     */
    public function setBody(string $txt): NotificationInterface
    {
        $this->body = $txt;

        return $this;
    }

    /**
     * set receivers identifiers function
     *
     * @param array $identifiers
     * @return NotificationInterface
     */
    public function receivers(array $identifiers): NotificationInterface
    {
        $this->tokens    = array_keys($identifiers);
        $this->receivers = $identifiers;

        return $this;
    }

    /**
     * send push function
     *
     * @return array
     */
    public function send(): array
    {
        if (empty(env("PUSH_PROVIDERS_PRIORITY")) || !env("PUSH_PROVIDERS_PRIORITY")) {
            throw new \Exception('Empty PUSH_PROVIDERS_PRIORITY config');
        }

        $providersPriority = explode(",", env("PUSH_PROVIDERS_PRIORITY"));

        if (!$providersPriority) {
            throw new \Exception('Invalid push providers config');
        }

        $finalResult = [
            'all_success'   => false,
            'success_count' => 0,
        ];

        foreach ($providersPriority as $pushProvider) {
            $provider = PushAbstract::resolve($pushProvider);

            if (!$provider) {
                continue;
            }

            $this->providerName = $pushProvider;

            $tokenChunks = array_chunk($this->tokens, 2000);

            foreach ($tokenChunks as $tokens) {
                $response = $provider->send(
                    $this->title,
                    $this->body,
                    $tokens,
                    $this->extra,
                    $this->expireAt
                );

                $finalResult = array_merge($finalResult, $response);

                if ($response["all_success"]) {
                    $this->updateLog($finalResult);

                    return $finalResult;
                }

                $tokens = [];
                foreach ($response['detail'] as $token => $value) {
                    if (!$value['success']) {
                        $tokens[] = $token;
                    }
                }
            }
        }

        $this->updateLog($finalResult);

        if ($finalResult['success_count'] == 0) {
            throw new SendPushFailedException(
                "Notifier: Sending Push failed",
                ", Text: " . $this->body,
                $this->tokens,
                $response['response']
            );
        }

        return $finalResult;

    }

    private function updateLog(array $result)
    {
        $notifications = Notification::whereIn('identifier', $this->tokens)
            ->where('body', trim($this->body))
            ->where('type', 1)
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
