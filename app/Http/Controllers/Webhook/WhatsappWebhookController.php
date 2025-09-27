<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Flow;
use App\Models\FlowStep;
use App\Models\Message;
use App\Models\ServiceNumber;
use Illuminate\Http\Request;
use Log;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;
use App\Enums\Flow\FlowStepExpectedAnswerTypeEnum;
use App\Enums\Flow\FlowStatusEnum;
use App\Enums\Conversation\ConversationStatusEnum;
use App\Enums\ServiceNumber\ServiceNumberStatusEnum;

class WhatsappWebhookController extends Controller
{
    protected $account_sid;
    protected $auth_token;
    protected $reminder_sid;
    protected $notification_sid;
    protected $message_sid;

    public function __construct()
    {
        $this->account_sid = config('twillio.account_sid');
        $this->auth_token = config('twillio.auth_token');
        $this->reminder_sid = config('twillio.templates.reminder');
        $this->notification_sid = config('twillio.templates.notification');
        $this->message_sid = config('twillio.templates.message');
    }

    public function handle(Request $request)
    {
        $client_phone = $request->input('From');   // ex: whatsapp:+201234567890
        $body = trim($request->input('Body'));
        $our_phone   = $request->input('To');     // رقمنا اللي وصله الرسالة

        Log::info($request->all());
        Log::info("📩 رسالة جديدة من {$client_phone}: {$body}");

        // 1) نجيب رقم الواتساب المستهدف
        $waNumber = ServiceNumber::with('flow')->where('phone_number', $our_phone)->first();
        if (!$waNumber) {
            Log::info("❌ Unknown number");
            return;
        }

        if ($waNumber->status != ServiceNumberStatusEnum::Active) {
            Log::info("❌ This Number is Out Of Service");
            return;
        }

        // 2) هل عنده Conversation Active؟
        $conversation = Conversation::where('user_phone', $client_phone)
            ->where('service_number_id', $waNumber->id)
            ->where('status', ConversationStatusEnum::Active)
            ->first();

        if (!$conversation) {
            // 3) مفيش → نبدأ فلو جديد (Default Flow)
            $flow = $waNumber->flow;

            if (!$flow) {
                $resp = new MessagingResponse();
                $resp->message("❌ لا يوجد Flow افتراضي للرقم ده.");
                return $resp;
            }

            if ($flow->status != FlowStatusEnum::Active) {
                $resp = new MessagingResponse();
                $resp->message("⚠️ هذا الـ Flow تم تعطيله حالياً.");
                return $resp;
            }

            $firstStep = $flow->steps()
                ->with(['flow', 'nextStep', 'answers'])
                ->where('is_start', true)
                ->first();

            $conversation = Conversation::create([
                'user_phone'        => $client_phone,
                'service_number_id' => $waNumber->id,
                'current_step_id'   => $firstStep->id,
                'status'            => ConversationStatusEnum::Active,
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'step_id'         => $firstStep->id,
                'user_message'    => $body,
                'bot_response'    => $firstStep->question_text,
            ]);

            $message = $this->generateMessages($firstStep);
            $resp = $this->sendMessage($message, $client_phone, $our_phone);

            return $resp;
        }

        // 4) لو عنده Conversation Active → نكمل
        $currentStep = $conversation->currentStep;

        // ✅ هنا التحقق من نوع الإجابة
        if (!$this->validateAnswer($currentStep, $body)) {
            $resp = new MessagingResponse();
            $resp->message($this->getErrorMessage($currentStep));
            return $resp;
        }

        // لو صح → نكمل للـ Next Step
        $nextStep = $currentStep->nextStep;

        if ($nextStep) {
            // حدث الـ Conversation
            $conversation->update(['current_step_id' => $nextStep->id]);

            // سجل في Conversation Log
            Message::create([
                'conversation_id' => $conversation->id,
                'step_id'         => $nextStep->id,
                'user_message'    => $body,
                'bot_response'    => $nextStep->question_text,
            ]);

            $resp = new MessagingResponse();
            $resp->message($nextStep->question_text);
            return $resp;
        } else {
            // لو دي آخر خطوة → انهي المحادثة
            $conversation->update(['status' => ConversationStatusEnum::Finished]);

            $resp = new MessagingResponse();
            $resp->message("✅ شكرًا، تم إنهاء المحادثة.");
            return $resp;
        }
    }


    private function validateAnswer($step, $message): bool
    {
        switch ($step->expected_answer_type) {
            case 'number':
                return is_numeric($message);
            case 'choice':
                $options = json_decode($step->options, true) ?? [];
                return array_key_exists($message, $options);
            case 'text':
                return !empty(trim($message));
            case 'any':
            default:
                return true;
        }
    }

    private function getErrorMessage($step): string
    {
        switch ($step->expected_answer_type) {
            case 'number':
                return "❌ من فضلك أدخل رقم صحيح.";
            case 'choice':
                return "❌ اختيار غير صحيح. حاول مرة أخرى.";
            case 'text':
                return "❌ الرد النصي مطلوب.";
            default:
                return "❌ إجابة غير صالحة.";
        }
    }

    public function generateMessages(FlowStep $step) {
        $message = "{$step->question_text}\n";

        if ($step->expected_answer_type == FlowStepExpectedAnswerTypeEnum::Choice) {
            foreach ($step->answers as $answer) {
                $message .= "{$answer->answer_value} : {$answer->answer_label}\n";
            }
        }
        return $message;
    }

    public function sendMessage($message, $customer_phone, $our_phone) {
        $sid    = $this->account_sid;
        $token  = $this->auth_token;
        $twilio = new Client($sid, $token);

        return $twilio->messages
            ->create($customer_phone, // to
                array(
                    "from" => $our_phone,
                    "body" => $message
                )
            );
    }

}
