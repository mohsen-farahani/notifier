<?php

namespace Asanbar\Notifier\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Asanbar\Notifier\NotificationProviders\PushProviders\PushAbstract;
use Illuminate\Support\Facades\Log;

class SendPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $heading;
    protected $content;
    protected $player_ids;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param $heading
     * @param string $content
     * @param array $player_ids
     * @param array $extra
     */
    public function __construct($heading, $content, $player_ids, $extra = null)
    {
        $this->heading = $heading;
        $this->content = $content;
        $this->player_ids = $player_ids;
        $this->data = $extra;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $push_providers_priority = explode(",", env("PUSH_PROVIDERS_PRIORITY"));

        if(!$push_providers_priority) {
            Log::error("Notifier: No PUSH_PROVIDERS_PRIORITY env available");

            return true;
        }

        foreach($push_providers_priority as $push_provider) {
            $provider = PushAbstract::resolve($push_provider);

            if(!$provider) {
                continue;
            }

            $response = $provider->send(
                $this->heading,
                $this->content,
                $this->player_ids,
                $this->data
            );

            if($response) {
                Log::info(
                    "Notifier: Push sent via " .
                    strtoupper($push_provider) .
                    ", Heading: " . $this->heading .
                    ", Content: " . $this->content .
                    ", Player Ids: " . implode(",", $this->player_ids)
                );

                break;
            }

            Log::warning("Notifier: Sending push failed via " .
                strtoupper($push_provider) .
                ", Heading: " . $this->heading .
                ", Content: " . $this->content .
                ", Player Ids: " . implode(",", $this->player_ids)
            );
        }
    }
}
