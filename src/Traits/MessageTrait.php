<?php

namespace AsanBar\Notifier\Traits;

use Asanbar\Notifier\Models\Message;
use Asanbar\Notifier\Models\Push;
use Asanbar\Notifier\NotificationProviders\MessageProviders\MessageAbstract;
use Illuminate\Support\Facades\Log;

trait MessageTrait
{
    protected $current_provider = null;

    public function sendMessage()
    {
        $message_providers_priority = explode(",", env("MESSAGE_PROVIDERS_PRIORITY"));

        if(!$message_providers_priority) {
            $this->logNoProvidersAvailable();

            return false;
        }

        foreach($message_providers_priority as $message_provider) {
            $current_provider = MessageAbstract::resolve($message_provider);

            if(!$current_provider) {
                continue;
            }

            $this->current_provider = $message_provider;

            $response = $current_provider->send(
                $this->title,
                $this->body,
                $this->user_ids
            );

            if(isset($response["result_id"]) && $response["result_id"] != null) {
                $this->logMessageSent();

                Message::createSentMessages(
                    $this->current_provider,
                    $this->user_ids,
                    $this->title,
                    $this->body,
                    $response["result_id"]
                );

                continue;
            }

            Message::createSendFailedMessages(
                $this->current_provider,
                $this->user_ids,
                $this->title,
                $this->body,
                $response
            );

            $this->logMessageSendFailed();
        }

        return false;
    }

    public function logNoProvidersAvailable()
    {
        Log::error("Notifier: No MESSAGE_PROVIDERS_PRIORITY env available");
    }

    public function logMessageSent()
    {
        Log::info(
            "Notifier: Message sent via " .
            strtoupper($this->current_provider) .
            ", Title: " . $this->title .
            ", Body: " . $this->body .
            ", User Ids: " . implode(",", $this->user_ids)
        );
    }

    public function logMessageSendFailed()
    {
        Log::warning("Notifier: Sending message failed via " .
            strtoupper($this->current_provider) .
            ", Title: " . $this->title .
            ", Body: " . $this->body .
            ", User Ids: " . implode(",", $this->user_ids)
        );
    }
}