<?php

namespace Asanbar\Notifier\Jobs;

use Asanbar\Notifier\NotificationService\NotifyService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $title;
    private $description;
    private $tokens;
    private $extra;
    private $expireAt;

    /**
     * Create a new job instance.
     *
     * @param string $title
     * @param string $description
     * @param array $tokens
     * @param array|null $extra
     * @param Carbon|null $expireAt
     */
    public function __construct(string $title, string $description, array $tokens, ?array $extra = null, ?Carbon $expireAt = null)
    {
        $this->title       = $title;
        $this->description = $description;
        $this->tokens      = $tokens;
        $this->extra       = $extra;
        $this->expireAt    = $expireAt;
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

            $notifier->setTitle($this->title)
                ->setBody($this->description)
                ->recievers($this->tokens)
                ->setExpireAt($this->expireAt)
                ->sendNotification('push');

        }
    }
}
