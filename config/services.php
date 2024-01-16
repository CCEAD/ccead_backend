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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'passport' => [
        'login_endpoint' => env('PASSPORT_LOGIN_ENDPOINT'),
        'permission' => env('PASSPORT_USER_PERMISSION'),
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET'),
    ],

    'server' => [
        'url' => env('BACKEND_ENDPOINT')
    ],

    'twilio' => [
        'twilio_sid' => env('TWILIO_SID'),
        'twilio_token' => env('TWILIO_TOKEN'),
        'twilio_from' => env('TWILIO_FROM'),
    ],

    'mongo' => [
        'host' => env('DB_HOST_MONGO'),
    ],

    'python' => [
        'scraping' => env('PY_SCRIPT_SCRAPING'),
        'dig1' => env('PY_SCRIPT_DIG1'),
        'dig2' => env('PY_SCRIPT_DIG2'),
    ]
];
