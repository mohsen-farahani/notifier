<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders;

use Carbon\Carbon;

interface SmsInterface
{
    /**
     * @param string $message
     * @param array $numbers
     * @param string|NULL $datetime
     * @param Carbon|null $expireAt
     * @return array
     */
    public function send(string $message, array $numbers, ?Carbon $expireAt = null): array;

    /**
     * receive messages function
     *
     * @return mixed[]
     */
    public function receive(): array;
}
