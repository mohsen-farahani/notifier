<?php

namespace Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\MessageProviders\Database;

use Asanbar\Notifier\NotificationService\Strategies\NotificationProviders\MessageProviders\MessageAbstract;
use Carbon\Carbon;

class DatabaseProvider extends MessageAbstract
{
    /**
     * @param string $title
     * @param string $body
     * @param array $reveivers
     * @param Carbon|null $expireAt
     * @return array
     */
    public function send(string $title, string $body, array $reveivers, ?Carbon $expireAt = null): array
    {
        $result = [
            'all_success'   => true,
            'success_count' => 0,
        ];

        foreach ($reveivers as $key => $token) {
            $result['detail'][$token] = [
                'success'  => true,
                'id'       => null,
                'provider' => 'database',
            ];
        }

        return $result;
    }
}
