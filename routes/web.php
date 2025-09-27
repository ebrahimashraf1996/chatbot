<?php

use Illuminate\Support\Facades\Route;
use Twilio\TwiML\MessagingResponse;
use Twilio\Rest\Client;

Route::get('/test', function () {
    $sid    = env('TWILIO_ACCOUNT_SID');
    $token  = env('TWILIO_AUTH_TOKEN');
    $twilio = new Client($sid, $token);

    $message = $twilio->messages
        ->create("whatsapp:+201147232702", // to
            array(
                "from" => "whatsapp:+15558741812",
                "contentSid" => "HXb5b62575e6e4ff6129ad7c8efe1f983e",
                "contentVariables" => "{"1":"12/1","2":"3pm"}",
                "body" => "Your Message"
        )
      );

    print($message->sid);

});
