<?php

return [

    'default' => env('MAIL_MAILER', 'sendmail'),

    'mailers' => [

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -t -i'),
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
