<?php

namespace AsanBar\Notifier\Traits;

use Asanbar\Notifier\Models\Sms;
use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;
use Illuminate\Support\Facades\Log;

trait SmsTrait
{
    protected $current_provider = null;

    public function sendSms()
    {
        $sms_providers_priority = explode(",", env("SMS_PROVIDERS_PRIORITY"));

        if(!$sms_providers_priority) {
            $this->logNoProvidersAvailable();

            return false;
        }

        foreach($sms_providers_priority as $sms_provider) {
            $current_provider = SmsAbstract::resolve($sms_provider);

            if(!$current_provider) {
                continue;
            }

            $this->current_provider = $sms_provider;

            $response = $current_provider->send(
                $this->message,
                $this->numbers,
                $this->datetime
            );

            if(isset($response["result_id"]) && $response["result_id"] !== null) {
                $this->logSmsSent();

                Sms::createSentSmses(
                    $this->current_provider,
                    $current_provider->from,
                    $this->numbers,
                    $this->message,
                    $response["result_id"]
                );

                return true;
            }

            Sms::createSendFailedSmses(
                $this->current_provider,
                $current_provider->from,
                $this->numbers,
                $this->message,
                $response
            );

            $this->logSmsSendFailed();
        }

        return true;
    }

    public function logNoProvidersAvailable()
    {
        Log::error("Notifier: No SMS_PROVIDERS_PRIORITY env available");
    }

    public function logSmsSent()
    {
        Log::info(
            "Notifier: SMS sent via " .
            strtoupper($this->current_provider) .
            ", Text: " . $this->message .
            ", To: " . implode(",", $this->numbers)
        );
    }

    public function logSmsSendFailed()
    {
        Log::warning("Notifier: Sending SMS failed via " .
            strtoupper($this->current_provider) .
            ", Text: " . $this->message .
            ", To: " . implode(",", $this->numbers)
        );
    }
}