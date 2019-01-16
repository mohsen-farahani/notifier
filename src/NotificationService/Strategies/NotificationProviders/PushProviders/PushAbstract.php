<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\PushProviders;

use Carbon\Carbon;
use Exception;

abstract class PushAbstract implements PushInterface
{
    /**
     * @param string $provider
     * @return bool|self
     */
    final public static function resolve(string $provider)
    {
        try {
            $provider_class = sprintf("%s%s%s%s%s",
                "Asanbar\\Notifier\\NotificationService\\Strategies\\NotificationProviders\\PushProviders\\",
                ucwords($provider),
                "\\",
                ucwords($provider),
                "Provider"
            );

            return new $provider_class;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $title
     * @param string $content
     * @param array $tokens
     * @param array|NULL $extra
     * @param Carbon|null $expireAt
     * @return array
     */
    abstract public function send(string $title, string $content, array $tokens, ?array $extra = null, ?Carbon $expireAt = null): array;
}
