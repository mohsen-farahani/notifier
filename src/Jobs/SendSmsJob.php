<?php

namespace Asanbar\Notifier\Jobs;

use Asanbar\Notifier\NotificationService\NotifyService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message;
    private $numbers;
    private $expireAt;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param array $numbers
     * @param Carbon|null $expireAt
     * @param array $options
     */
    public function __construct(string $message, array $numbers, ?Carbon $expireAt = null)
    {
        $this->message  = $message;
        $this->numbers  = $numbers;
        $this->expireAt = $expireAt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->expireAt === null || !Carbon::now()->gt($this->expireAt)) { //if expireAt is zero or now not greater than expireAt
            $notifier = app(NotifyService::class);

            $notifier->setBody($this->message)
                ->recievers($this->numbers)
                ->sendNotification('sms');
        }
    }
}
