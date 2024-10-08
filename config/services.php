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

    'zitadel' => [
        'client_id' => env('ZITADEL_CLIENT_ID'),
        'client_secret' => '',
        'redirect' => env('ZITADEL_REDIRECT_URL'),
        'base_url' => env('ZITADEL_BASE_URL'),
        'organization_id' => env('ZITADEL_ORGANIZATION_ID'),                      // Optional
        'project_id' => env('ZITADEL_PROJECT_ID'),                                // Optional
        'post_logout_redirect_uri' => env('ZITADEL_POST_LOGOUT_REDIRECT_URI')     // Optional
    ],

];
