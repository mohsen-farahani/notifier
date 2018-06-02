<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders;

interface SmsInterface
{
    public function send(array $messages, array $numbers, string $datetime = null);
}