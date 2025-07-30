<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'establecimiento' => [
        'codigo' => '06206',
        'timeout' => 10
    ],

    'mefMicro_Produccion' => [
        'base_url' => 'https://servicios.minsa.gob.pe/mcs-servicios-refcon/servicio/v1.0.0',
        'username' => 'USR_00006206',
        'password' => 'Hn4c10nal@2M4y0',
        'ipclient'    => '190.12.76.25',
    ],

    'mefMicro_Beta' => [
        'base_url' => 'https://dservicios.minsa.gob.pe/mcs-servicios-refcon/servicio/v1.0.0',
        'username' => 'USR_00006206',
        'password' => 'demo@2025@',
        'ipclient'    => '190.12.76.25',
    ],
];
