<?php

return [
    "sms" => [
        "smsir" => [
            "api_key" => env("SMSIR_API_KEY", "5388b3293883be495c0ab329"),
            "line_number" => env("SMSIR_LINE_NUMBER", "30004505001512"),
            "secret_key" => env("SMSIR_SECRET_KEY", "@FEW46545sddfdsf!@#%^&*(sanbar"),
        ],
        "sms0098" => [
            "from" => env("SMS0098_FROM", "300061324321"),
            "username" => env("SMS0098_USERNAME", "nsms9497"),
            "password" => env("SMS0098_PASSWORD", "09122606129"),
        ]
    ],

    "push" => [
        "onesignal" => [
            "app_id" => env("ONESIGNAL_APP_ID", "ecb8be7c-f1f5-47ca-95c1-a8949113c0bd"),
            "authorization" => env("ONESIGNAL_AUTHORIZATION", "MGQ5OTEzZWYtN2EwNC00YjIyLWI4MGYtNWM1NzBhNWU0ZmYy"),
        ]

    ]
];