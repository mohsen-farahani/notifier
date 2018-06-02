<?php

namespace Asanbar\Notifier\NotificationProviders\SmsProviders;

abstract class SmsAbstract implements SmsInterface
{
    abstract function send(array $messages, array $numbers, string $datetime = null);
}