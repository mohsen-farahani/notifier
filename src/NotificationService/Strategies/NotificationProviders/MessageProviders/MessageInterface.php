<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\MessageProviders;

use Carbon\Carbon;

interface MessageInterface
{
    /**
     * @param string $title
     * @param string $body
     * @param array $reveivers
     * @param Carbon|null $expireAt
     * @return array
     */
    public function send(string $title, string $body, array $reveivers, ?Carbon $expireAt = null): array;
}
