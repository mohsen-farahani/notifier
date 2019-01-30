<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\MessageProviders;

use Carbon\Carbon;
use Exception;

abstract class MessageAbstract implements MessageInterface
{
    /**
     * @param string $provider
     * @return bool|self
     */
    final public static function resolve(string $provider)
    {
        try {
            $provider_class = sprintf("%s%s%s%s%s",
                "Asanbar\\Notifier\\NotificationService\\Strategies\\NotificationProviders\\MessageProviders\\",
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
     * @param string $body
     * @param array $reveivers
     * @param Carbon|null $expireAt
     * @return array
     */
    abstract public function send(string $title, string $body, array $reveivers, ?Carbon $expireAt = null): array;
}
