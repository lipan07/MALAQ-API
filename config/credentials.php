<?php

return [
    'smtp' => [
        'host' => env('SMTP_HOST'),
        'port' => env('SMTP_PORT'),
        'username' => env('SMTP_USERNAME'),
        'password' => env('SMTP_PASSWORD'),
    ],
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'number' => env('TWILIO_NUMBER')
    ],
];
