<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\SmsProviders;

use Carbon\Carbon;
use Exception;

abstract class SmsAbstract implements SmsInterface
{
    public $from;

    /**
     * @param string $provider
     * @return bool|self
     */
    final public static function resolve(string $provider)
    {
        try {
            $provider_class = sprintf("%s%s%s%s%s",
                "Asanbar\\Notifier\\NotificationService\\Strategies\\NotificationProviders\\SmsProviders\\",
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
     * @param string $message
     * @param array $numbers
     * @param Carbon|null $expireAt
     * @return array
     */
    abstract public function send(string $message, array $numbers, ?Carbon $expireAt = null): array;
}
