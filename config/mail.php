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

  // TEMP bypass if server keeps failing cert verify:
  'stream' => [
    'ssl' => [
      'verify_peer' => false,
      'verify_peer_name' => false,
      'allow_self_signed' => false,
    ],
  ],
],


        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL', 'stack'),
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreplyshiroestate@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'Shiro Estate'),
    ],

];
