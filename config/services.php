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

    'hitobito' => [
        'base_url' => env('HITOBITO_BASE_URL', 'http://demo.hitobito.com'),
        'client_id' => env('HITOBITO_CLIENT_UID'),
        'client_secret' => env('HITOBITO_CLIENT_SECRET'),
        'redirect' => env('HITOBITO_CALLBACK_URI', 'https://demo.hitobito.com/login/hitobito/callback'),
    ],

    'hitobito_jemk' => [
        'base_url' => env('JEMK_BASE_URL', 'http://demo.hitobito.com'),
        'client_id' => env('JEMK_CLIENT_UID'),
        'client_secret' => env('JEMK_CLIENT_SECRET'),
        'redirect' => env('JEMK_CALLBACK_URI', 'https://demo.hitobito.com/login/hitobito/callback'),
    ],


];
