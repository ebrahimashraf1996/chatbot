<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Flow;
use App\Models\FlowStep;
use App\Models\ServiceNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\ServiceNumber\ServiceNumberStatusEnum;
use App\Enums\Flow\FlowStatusEnum;
use App\Enums\Flow\FlowStepExpectedAnswerTypeEnum;

class FlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Create Flow
        $flow = Flow::create([
            'name' => 'Welcome Flow',
            'description' => 'Flow to welcome customers and guide them to sales or support',
            'status' => FlowStatusEnum::Active,
        ]);

        $endingTextStep = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'شكرا لكم .. سيتم التواصل معكم في أقرب وقت ممكن',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::End,
            'next_step_id' => null,
        ]);


        // Step 1: Welcome
        $step1 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => '👋 أهلاً بيك في شركتنا! اقدر اساعدك ازاي؟',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Choice,
            'next_step_id' => null,
            'is_start' => true,
        ]);

        $step2 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'من فضلك ادخل رقم موبايل للتواصل',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Text,
            'next_step_id' => null,
        ]);
        $step3 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'من فضلك ادخل رقم الطلب',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Number,
            'next_step_id' => $endingTextStep->id,
        ]);

        $step2->update([
            'next_step_id' => $step3->id,
        ]);




        $step4 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'يرجي التواصل علي الرقم التالي لتحديد أقرب فرع +201147232702',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Text,
            'next_step_id' => null,
        ]);

        $step5 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'يرجي تحديد موضوع التواصل',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Choice,
            'next_step_id' => null,
        ]);

        $step6 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'يرجي توضيح المشكلة بشكل تفصيلي',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Text,
            'next_step_id' => $endingTextStep->id,
        ]);

        $step7 = FlowStep::create([
            'flow_id' => $flow->id,
            'question_text' => 'يرجي توضيح النشاط التجاري',
            'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::Text,
            'next_step_id' => $endingTextStep->id,
        ]);


        // Step 1 Options
        $answer1 = Answer::create([
            'flow_step_id' => $step1->id,
            'answer_value' => 1,
            'answer_label' => 'خدمة العملاء',
            'next_step_id' => $step2->id,
        ]);


        // Step 1 Options
        $answer2 = Answer::create([
            'flow_step_id' => $step1->id,
            'answer_value' => 2,
            'answer_label' => 'قسم المبيعات',
            'next_step_id' => $step4->id,
        ]);
        // Step 1 Options
        $answer3 = Answer::create([
            'flow_step_id' => $step1->id,
            'answer_value' => 3,
            'answer_label' => 'مجلس ادارة الشركة',
            'next_step_id' => $step5->id,
        ]);



        // Step 5 Options
        $answer4 = Answer::create([
            'flow_step_id' => $step5->id,
            'answer_value' => 1,
            'answer_label' => 'شكوي عامة',
            'next_step_id' => $step6->id,
        ]);

        // Step 5 Options
        $answer5 = Answer::create([
            'flow_step_id' => $step5->id,
            'answer_value' => 2,
            'answer_label' => 'طلب شراكة',
            'next_step_id' => $step7->id,
        ]);




        $serviceNumber = ServiceNumber::create([
            'flow_id' => $flow->id,
            'name' => 'رقم خدمة عملاء',
            'phone_number' => 'whatsapp:+15558741812',
            'twilio_sid' => env('TWILIO_ACCOUNT_SID'),
            'twilio_token' => env('TWILIO_AUTH_TOKEN'),
            'status' => ServiceNumberStatusEnum::Active
        ]);

    }
}
