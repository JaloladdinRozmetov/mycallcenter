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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'tts' => [
        'endpoint' => env('TTS_ENDPOINT'),
        'api_key' => env('TTS_API_KEY'),
        'voice' => env('TTS_VOICE', 'default'),
        'language' => env('TTS_LANGUAGE', 'en-US'),
        'format' => env('TTS_FORMAT', 'wav'),
        'timeout' => env('TTS_TIMEOUT', 15),
    ],

    'ari' => [
        'base_uri' => env('ASTERISK_ARI_BASE_URI'),
        'username' => env('ASTERISK_ARI_USERNAME'),
        'password' => env('ASTERISK_ARI_PASSWORD'),
        'app' => env('ASTERISK_ARI_APP', 'outbound'),
        'outbound_endpoint' => env('ASTERISK_ARI_OUTBOUND_ENDPOINT', 'PJSIP/provider'),
        'caller_id' => env('ASTERISK_ARI_CALLER_ID', 'CallCenter'),
        'dial_timeout' => env('ASTERISK_ARI_DIAL_TIMEOUT', 20),
    ],

];
