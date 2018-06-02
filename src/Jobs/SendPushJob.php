<?php

namespace Asanbar\Notifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;

class SendPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pushProvider;

    protected $heading;
    protected $content;
    protected $player_ids;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param PushAbstract $pushProvider
     * @param string $heading
     * @param string $content
     * @param array $player_ids
     * @param array $extra
     */
    public function __construct(PushAbstract $pushProvider, $heading, $content, $player_ids, $extra = null)
    {
        $this->pushProvider = $pushProvider;

        $this->heading = $heading;
        $this->content = $content;
        $this->player_ids = $player_ids;
        $this->data = $extra;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pushProvider->send(
            $this->heading,
            $this->content,
            $this->player_ids,
            $this->data
        );
    }
}
