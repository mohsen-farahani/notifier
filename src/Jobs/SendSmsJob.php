<?php

namespace Asanbar\Notifier\Jobs;

use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $numbers;
    protected $datetime;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param array $numbers
     * @param string $datetime
     */
    public function __construct($message, $numbers, $datetime = null)
    {
        $this->message = $message;
        $this->numbers = $numbers;
        $this->datetime = $datetime;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $sms_providers_priority = explode(",", env("SMS_PROVIDERS_PRIORITY"));

        if(!$sms_providers_priority) {
            Log::error("Notifier: No SMS_PROVIDERS_PRIORITY env available");

            return true;
        }

        foreach($sms_providers_priority as $sms_provider) {
            $provider = SmsAbstract::resolve($sms_provider);

            if(!$provider) {
                continue;
            }

            $response = $provider->send(
                $this->message,
                $this->numbers,
                $this->datetime
            );

            if($response) {
                Log::info(
                    "Notifier: SMS sent via " .
                    strtoupper($sms_provider) .
                    ", Text: " . $this->message .
                    ", To: " . implode(",", $this->numbers)
                );

                break;
            }

            Log::warning("Notifier: Sending SMS failed via " .
                strtoupper($sms_provider) .
                ", Text: " . $this->message .
                ", To: " . implode(",", $this->numbers)
            );
        }

        return true;
    }
}
