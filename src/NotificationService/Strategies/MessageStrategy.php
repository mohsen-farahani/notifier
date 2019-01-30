<?php

namespace Asanbar\Notifier\NotificationService\Strategies;

use Asanbar\Notifier\Models\Message;
use Asanbar\Notifier\Models\Notification;
use Asanbar\Notifier\NotificationService\NotificationInterface;
use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\MessageProviders\MessageAbstract;
use Carbon\Carbon;

class MessageStrategy implements NotificationInterface
{
    /** @var string provider name */
    private $providerName = null;

    /** @var string title  */
    private $title;

    /** @var string body */
    private $body;

    /** @var string[] numbers */
    private $numbers;

    /** @var string[] receivers */
    private $receivers;

    /** @var Carbon expire date time */
    private $expireAt;

    /**
     * set expire at function
     *
     * @param Carbon|null $time
     * @return NotificationInterface
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
        $this->numbers   = array_keys($identifiers);
        $this->receivers = $identifiers;

        return $this;
    }

    /**
     * send message function
     *
     * @return array
     */
    public function send(): array
    {
        $messageProviders = explode(",", env("MESSAGE_PROVIDERS_PRIORITY", 'Database'));

        $finalResult = [];
        foreach ($messageProviders as $messageProvider) {
            $currentProvider = MessageAbstract::resolve($messageProvider);

            if (!$currentProvider) {
                continue;
            }

            $this->currentProvider = $messageProvider;

            $response = $currentProvider->send(
                $this->title,
                $this->body,
                $this->receivers,
                $this->expireAt
            );

            $finalResult = array_merge($finalResult, $response);

            if ($response["all_success"]) {
                $this->updateLog($finalResult);

                return $finalResult;
            }

            $numbers = [];
            foreach ($response['detail'] as $number => $value) {
                if (!$value['success']) {
                    $numbers[] = $number;
                }
            }
        }

        $this->updateLog($finalResult);

        if ($finalResult['success_count'] == 0) {
            throw new SendSMSFailedException(
                "Notifier: Sending SMS failed",
                ", Text: " . $this->message,
                $this->numbers,
                $response['response']
            );
        }

        return $finalResult;
    }

    private function updateLog(array $result)
    {
        $notifications = Notification::whereIn('identifier', $this->receivers)
            ->where('body', trim($this->body))
            ->where('type', 2)
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
