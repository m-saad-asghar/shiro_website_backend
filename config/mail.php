<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,

            // Optional (only if you REALLY need to bypass SSL checks)
            // 'stream' => [
            //     'ssl' => [
            //         'allow_self_signed' => true,
            //         'verify_peer' => false,
            //         'verify_peer_name' => false,
            //     ],
            // ],
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL', 'stack'),
        ],

    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreplyshiroestate@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'Shiro Estate'),
    ],

];