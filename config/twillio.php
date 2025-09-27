<?php

return [
    'account_sid' => env('TWILIO_ACCOUNT_SID'),
    'auth_token'  => env('TWILIO_AUTH_TOKEN'),
    'from'        => env('TWILIO_WHATSAPP_DEFAULT_FROM'),

    'templates' => [
        'reminder'     => env('REMINDER_SID'),
        'notification' => env('NOTIFICATION_SID'),
        'message'      => env('MESSAGE_SID'),
    ],
];
