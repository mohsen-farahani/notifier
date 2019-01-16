<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\PushProviders;

use Carbon\Carbon;

interface PushInterface
{
    /**
     * @param string $title
     * @param string $content
     * @param array $tokens
     * @param array|NULL $extra
     * @param Carbon|null $expireAt
     * @return array
     */
    public function send(string $title, string $content, array $tokens, ?array $extra = null, ?Carbon $expireAt = null): array;
}
