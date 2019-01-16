<?php

namespace Asanbar\Notifier\NotificationService\Strategies;

use Asanbar\Notifier\Exceptions\SendSMSFailedException;
use Asanbar\Notifier\Models\Notification;
use Asanbar\Notifier\NotificationService\NotificationInterface;
use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders\SmsAbstract;
use Carbon\Carbon;

class SMSStrategy implements NotificationInterface
{
    /** @var string provider name */
    private $providerName = null;

    /** @var string title  */
    private $title;

    /** @var string message */
    private $message;

    /** @var string[] numbers */
    private $numbers;

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
        $this->message = $txt;

        return $this;
    }

    /**
     * set recievers identifiers function
     *
     * @param array $identifiers
     * @return NotificationInterface
     */
    public function recievers(array $identifiers): NotificationInterface
    {
        $this->numbers = $identifiers;

        return $this;
    }

    /**
     * send sms function
     *
     * @return array
     */
    public function send(): array
    {
        if (empty(env("SMS_PROVIDERS_PRIORITY")) || !env("SMS_PROVIDERS_PRIORITY")) {
            throw new \Exception('Notifier: SMS_PROVIDERS_PRIORITY not found!');
        }

        $providersPriority = explode(",", env("SMS_PROVIDERS_PRIORITY"));

        if (!$providersPriority) {
            throw new \Exception('Notifier: No SMS_PROVIDERS_PRIORITY env available');
        }

        $finalResult = [
            'all_success'   => false,
            'success_count' => 0,
        ];

        $numbers = $this->numbers;
        foreach ($providersPriority as $provider) {
            $activeProvider = SmsAbstract::resolve($provider);

            if (!$activeProvider) {
                continue;
            }

            $this->providerName = $provider;

            $response = $activeProvider->send(
                $this->message,
                $numbers,
                $this->expireAt
            );

            $finalResult = array_merge($finalResult, $response);

            if ($response["all_success"]) {
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
        $notifications = Notification::whereIn('identifier', $this->numbers)
            ->where('body', trim($this->message))
            ->where('type', 0)
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
