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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'simbrief' => [
        'base_url' => env('SIMBRIEF_BASE_URL', 'https://www.simbrief.com/api/xml.fetcher.php'),
        'json_mode' => env('SIMBRIEF_JSON_MODE', 'v2'),
        'performance_url' => env('SIMBRIEF_PERFORMANCE_URL', 'https://www.simbrief.com/system/dispatch.php?section=perf'),
    ],

    'discord' => [
        'enabled' => env('DISCORD_OAUTH_ENABLED', true),
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'guild_id' => env('DISCORD_GUILD_ID'),
        'bot_token' => env('DISCORD_BOT_TOKEN'),
        'roles' => [
            'pilot' => env('DISCORD_ROLE_PILOT'),
            'staff' => array_filter(explode(',', (string) env('DISCORD_ROLE_STAFF'))),
        ],
    ],

    'vamsys' => [
        'enabled' => env('VAMSYS_OAUTH_ENABLED', false),
        'client_id' => env('VAMSYS_CLIENT_ID'),
        'client_secret' => env('VAMSYS_CLIENT_SECRET'),
        'redirect' => env('VAMSYS_REDIRECT_URI'),
        'base_url' => env('VAMSYS_BASE_URL', 'https://vamsys.io/api/v3/operations'),
        'token_url' => env('VAMSYS_TOKEN_URL', 'https://vamsys.io/oauth/token'),
        'web_booking_url' => env('VAMSYS_WEB_BOOKING_URL', 'https://vamsys.io/operations/bookings/{id}'),
    ],

    'weather' => [
        'base_url' => env('AVIATION_WEATHER_BASE_URL', 'https://aviationweather.gov/api/data'),
    ],

];
