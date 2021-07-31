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
        // 'key' => env('AWS_ACCESS_KEY_ID'),
        // 'secret' => env('AWS_SECRET_ACCESS_KEY'),
        // 'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),

        'key'    => env('SES_KEY', ''),
        'secret' => env('SES_KEY_SECRET', ''),
        'region' => env('SES_REGION', 'ap-south-1'),
        'bucket_url' => env('AWS_BUCKET_URL', ''),
        'host' => env('SES_EMAIL_HOST', 'email-smtp.ap-south-1.amazonaws.com'),
        'email' => env('SES_SENDER_EMAIL', ''),
        'username' => env('SES_USERNAME', ''),
        'password' => env('SES_PASSWORD', ''),
        'fromname' => env('SES_FROM_NAME', ''),

    ],
    'miscellaneous' => [
        'kff_logo_url' => env('KFF_LOGO_URL', ''),
        'ios_logo_url' => env('IOS_LOGO_URL', ''),
        'android_logo_url' => env('ANDROID_LOGO_URL', ''),
        'SMS_VALIDITY_TIME_MINUTES' => env('SMS_VALIDITY_TIME_MINUTES', 10),
        'FROM_NO' => env('FROM_NO', 10),
        'SMS_GATEWAY_API_BASE_URL' => env('SMS_GATEWAY_API_BASE_URL', ''),
        'SMS_GATEWAY_API_AUTH_KEY' => env('SMS_GATEWAY_API_AUTH_KEY', ''),
        'SMS_GATEWAY_API_SENDER' => env('SMS_GATEWAY_API_SENDER', ''),
        'EMAIL_VERIFY_URL' => env('EMAIL_VERIFY_URL', ''),
        'CIPHERING' => env('CIPHERING', ''),
        'ENCRYPTION_IV' => env('ENCRYPTION_IV', ''),
        'HASH_KEY' => env('HASH_KEY', '')
    ]

];
