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
     * @param $request
     */
    public function __construct(PushAbstract $pushProvider, $request)
    {
        $this->pushProvider = $pushProvider;

        $this->heading = $request["heading"];
        $this->content = $request["content"];
        $this->player_ids = $request["player_ids"];
        $this->data = $request["extra"];
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
