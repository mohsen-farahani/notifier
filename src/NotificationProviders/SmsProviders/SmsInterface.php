<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders;

interface SmsInterface
{
    public function send(string $message, array $numbers, string $datetime = null);
}