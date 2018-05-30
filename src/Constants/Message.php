<?php

namespace Asanbar\Notifier\Constants;

class Message
{
    const PUSH_NOT_SUPPORTED = [
        "code" => 1001,
        "message" => "Push driver is not supported"
    ];

    const PUSH_REQUEST_REGISTERED = [
        "code" => 1002,
        "message" => "Push request was registered"
    ];
}