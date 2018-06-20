<?php

namespace Asanbar\Notifier\Jobs;

use Asanbar\Notifier\Traits\MessageTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use MessageTrait;

    protected $title;
    protected $body;
    protected $user_ids;

    /**
     * Create a new job instance.
     *
     * @param $title
     * @param $body
     * @param $user_ids
     */
    public function __construct($title, $body, $user_ids)
    {
        $this->title = $title;
        $this->body = $body;
        $this->user_ids = $user_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendMessage();
    }
}
