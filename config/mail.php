<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

       'smtp' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', '127.0.0.1'),
    'port' => env('MAIL_PORT', 25),
    'encryption' => null,   // important
    'username' => null,
    'password' => null,
    'timeout' => null,

    // This disables TLS verification if Exim offers STARTTLS
    'stream' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ],
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
