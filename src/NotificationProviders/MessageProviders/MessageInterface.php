<?php

namespace Asanbar\Notifier\NotificationProviders\MessageProviders;


interface MessageInterface
{
    public function send(string $title, string $body);
}