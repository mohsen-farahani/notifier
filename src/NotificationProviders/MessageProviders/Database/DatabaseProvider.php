<?php

namespace Asanbar\Notifier\NotificationProviders\MessageProviders\Database;

use Asanbar\Notifier\NotificationProviders\MessageProviders\MessageAbstract;

class DatabaseProvider extends MessageAbstract
{
    public function send(string $title, string $body)
    {
        return [
            "result_id" => "success",
            "errors" => [],
        ];
    }
}