<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Flow;
use App\Models\Message;
use App\Models\ServiceNumber;
use Illuminate\Http\Request;
use Log;
use Twilio\TwiML\MessagingResponse;
use App\Enums\Flow\FlowStatusEnum;
use App\Enums\Conversation\ConversationStatusEnum;
use App\Enums\ServiceNumber\ServiceNumberStatusEnum;

class WhatsappWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $from    = $request->input('From');   // ex: whatsapp:+201234567890
        $body    = trim($request->input('Body'));
        $to      = $request->input('To');     // رقمنا اللي وصله الرسالة


        Log::info($request->all());
        Log::info("أهلاً! استقبلنا رسالتك: {$body}");


        // 1) نجيب رقم الواتساب المستهدف
        $waNumber = ServiceNumber::with('flow')->where('phone_number', $to)->first();
        if (!$waNumber) {
            Log::info("Unknown number");
            return;
        }

        if ($waNumber->status != ServiceNumberStatusEnum::Active) {
            Log::info("This Number is Out Of Service");
            return;
        }

        // 2) هل عنده Conversation Active؟
        $conversation = Conversation::where('user_phone', $from)
            ->where('service_number_id', $waNumber->id)
            ->where('status', ConversationStatusEnum::Active)
            ->first();

        if (!$conversation) {
            // 3) مفيش → نبدأ فلو جديد (Default Flow)
            $flow = $waNumber->flow;

            if (!$flow) {
                $resp = new MessagingResponse();
                $resp->message("لا يوجد Flow افتراضي للرقم ده.");
                Log::info($resp);
            return;
            }

            if ($flow->status != FlowStatusEnum::Active) {
                Log::info("This Flow Has Been Disabled");
            return;
            }


            $firstStep = $flow->steps()->orderBy('step_order')->first();

            $conversation = Conversation::create([
                'user_phone' => $from,
                'service_number_id' => $waNumber->id,
                'current_step_id' => $firstStep->id,
                'status' => ConversationStatusEnum::Active,
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'step_id' => $firstStep->id,
                'user_message' => $body,
                'bot_response' => $firstStep->question_text,
            ]);

            $resp = new MessagingResponse();
            $resp->message($firstStep->question_text);
            Log::info($resp);
        return;
        }

        // 4) لو عنده Conversation Active → نكمل
        $currentStep = $conversation->currentStep;

        // هنا ممكن نتحقق من expected_answer_type (هنعملها في Step Engine بعدين)
        $nextStep = $currentStep->nextStep;

        if ($nextStep) {
            // حدث الـ Conversation
            $conversation->update(['current_step_id' => $nextStep->id]);

            // سجل في Conversation Log
            Message::create([
                'conversation_id' => $conversation->id,
                'step_id' => $nextStep->id,
                'user_message' => $body,
                'bot_response' => $nextStep->question_text,
            ]);

            $resp = new MessagingResponse();
            $resp->message($nextStep->question_text);
            Log::info($resp);
        return;
        } else {
            // لو دي آخر خطوة → انهي المحادثة
            $conversation->update(['status' => ConversationStatusEnum::Finished]);

            $resp = new MessagingResponse();
            $resp->message("شكرًا، تم إنهاء المحادثة ✅");
            Log::info($resp);
        return;
        }

    }

}
