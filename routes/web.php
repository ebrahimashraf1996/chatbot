<?php

use Illuminate\Support\Facades\Route;
use Twilio\TwiML\MessagingResponse;

Route::get('/test', function () {
    $resp = new MessagingResponse();
    $resp->message('test');
});
