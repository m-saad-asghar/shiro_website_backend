<?php

return [

    'default' => env('MAIL_MAILER', 'sendmail'),

    'mailers' => [

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -t -i'),
        ],

        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 25),
            'encryption' => env('MAIL_ENCRYPTION'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL', 'stack'),
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'enquiry@shiroestate.ae'),
        'name' => env('MAIL_FROM_NAME', 'Shiro Estate'),
    ],
];
