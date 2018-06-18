<?php

namespace Asanbar\Notifier\Jobs;

use Asanbar\Notifier\Traits\SmsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SmsTrait;

    protected $message;
    protected $numbers;
    protected $datetime;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param array $numbers
     */
    public function __construct($message, $numbers)
    {
        $this->message = $message;
        $this->numbers = $numbers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendSms();
    }
}
