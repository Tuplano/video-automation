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

    'veo' => [
        'video_generator_url' => env('VEO_VIDEO_GENERATOR_URL'),
        'video_page_url' => env('VEO_VIDEO_PAGE_URL'),
        'headers' => [
            'accept' => env('VEO_ACCEPT_HEADER'),
            'accept_encoding' => env('VEO_ACCEPT_ENCODING_HEADER'),
            'accept_language' => env('VEO_ACCEPT_LANGUAGE_HEADER'),
            'cache_control' => env('VEO_CACHE_CONTROL_HEADER'),
            'cookie' => env('VEO_COOKIE_HEADER'),
            'priority' => env('VEO_PRIORITY_HEADER'),
            'referer' => env('VEO_REFERER_HEADER'),
            'sec_ch_ua' => env('VEO_SEC_CH_UA_HEADER'),
            'sec_ch_ua_mobile' => env('VEO_SEC_CH_UA_MOBILE_HEADER'),
            'sec_ch_ua_platform' => env('VEO_SEC_CH_UA_PLATFORM_HEADER'),
            'sec_fetch_dest' => env('VEO_SEC_FETCH_DEST_HEADER'),
            'sec_fetch_mode' => env('VEO_SEC_FETCH_MODE_HEADER'),
            'sec_fetch_site' => env('VEO_SEC_FETCH_SITE_HEADER'),
            'sec_fetch_user' => env('VEO_SEC_FETCH_USER_HEADER'),
            'sec_gpc' => env('VEO_SEC_GPC_HEADER'),
            'upgrade_insecure_requests' => env('VEO_UPGRADE_INSECURE_REQUESTS_HEADER'),
            'user_agent' => env('VEO_USER_AGENT_HEADER'),
            'x_requested_with' => env('VEO_X_REQUESTED_WITH_HEADER'),
            'origin' => env('VEO_ORIGIN_HEADER'),
        ],
    ],

];
