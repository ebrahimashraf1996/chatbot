<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Flow\FlowStepExpectedAnswerTypeEnum;

class FlowStep extends Model
{
    use HasUuids;

    protected $fillable = [
        'flow_id', 'step_order', 'question_text',
        'expected_answer_type', 'options', 'next_step_id'
    ];

    protected $casts = [
        'options' => 'array',
        'step_order' => 'integer',
        'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::class
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function nextStep()
    {
        return $this->belongsTo(FlowStep::class, 'next_step_id');
    }

}
