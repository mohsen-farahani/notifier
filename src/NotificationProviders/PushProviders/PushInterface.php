<?php

namespace Asanbar\Notifier\NotificationProviders\PushProviders;


interface PushInterface
{
    public function send(string $heading, string $content, array $player_ids, $extra = null);
}