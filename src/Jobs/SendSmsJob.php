<?php

namespace Asanbar\Notifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Asanbar\Notifier\NotificationProviders\SmsProviders\SmsAbstract;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sms_provider;

    protected $messages;
    protected $numbers;
    protected $datetime;

    /**
     * Create a new job instance.
     *
     * @param SmsAbstract $sms_provider
     * @param array $messages
     * @param array $numbers
     * @param string $datetime
     */
    public function __construct(SmsAbstract $sms_provider, $messages, $numbers, $datetime = null)
    {
        $this->sms_provider = $sms_provider;

        $this->messages = $messages;
        $this->numbers = $numbers;
        $this->datetime = $datetime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sms_provider->send(
            $this->messages,
            $this->numbers,
            $this->datetime
        );
    }
}
