<?php

return [
    "sms" => [
        "sms0098" => [
            "from" => env("SMS0098_FROM"),
            "username" => env("SMS0098_USERNAME"),
            "password" => env("SMS0098_PASSWORD"),
        ],
        "smsir" => [
            "api_key" => env("SMSIR_API_KEY"),
            "line_number" => env("SMSIR_LINE_NUMBER"),
            "secret_key" => env("SMSIR_SECRET_KEY"),
        ]
    ],

    "push" => [
        "onesignal" => [
            "app_id" => env("ONESIGNAL_APP_ID"),
            "authorization" => env("ONESIGNAL_AUTHORIZATION"),
        ]

    ]
];