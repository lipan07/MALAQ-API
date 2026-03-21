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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'fcm' => [
        'key' => env('FCM_SERVER_KEY')
    ],

    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS'),
    ],

    'google_pay' => [
        'merchant_id' => env('GOOGLE_PAY_MERCHANT_ID'),
        'merchant_name' => env('GOOGLE_PAY_MERCHANT_NAME', 'nearX'),
        'environment' => env('GOOGLE_PAY_ENVIRONMENT', 'TEST'), // TEST or PRODUCTION
        'gateway' => env('GOOGLE_PAY_GATEWAY', 'razorpay'), // razorpay, payu, stripe, etc.
        'gateway_merchant_id' => env('GOOGLE_PAY_GATEWAY_MERCHANT_ID'),
    ],

];
