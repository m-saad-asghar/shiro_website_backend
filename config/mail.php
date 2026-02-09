<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */
    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */
    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',

            // For Gmail use smtp.gmail.com, for cPanel use mail.shiroestate.ae / mail.shirovps.com
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 465),

            // 465 => ssl, 587 => tls
            'encryption' => env('MAIL_ENCRYPTION', 'ssl'),

            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),

            'timeout' => null,
            'auth_mode' => null,

            /*
             * TEMP BYPASS (remove later):
             * Some servers have PHP stream TLS issues even when openssl CLI works.
             */
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,

                    // Optional: force TLS 1.2 if TLS1.3 causes issues
                    // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                ],
            ],
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp', 'log'],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['ses', 'postmark'],
            'retry_after' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@shiroestate.ae'),
        'name' => env('MAIL_FROM_NAME', 'Shiro Estate'),
    ],

];
