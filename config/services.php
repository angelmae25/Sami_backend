<?php

return [
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

    'sami_otp' => [
        'driver' => env('SAMI_OTP_MAILER', 'smtp'),
        'apps_script_url' => env('SAMI_APPS_SCRIPT_URL'),
        'apps_script_token' => env('SAMI_APPS_SCRIPT_TOKEN'),
    ],
    'sami_id_verification' => [
        'provider' => env('SAMI_ID_VERIFICATION_PROVIDER', 'gemini'),
        'gemini_api_key' => env('GEMINI_API_KEY'),
        'gemini_model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
        'gemini_fallback_models' => env('GEMINI_FALLBACK_MODELS', 'gemini-3.5-flash,gemini-3.7-flash'),
    ],
];
