<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Flow\FlowStepExpectedAnswerTypeEnum;

class FlowStep extends Model
{
    use HasUuids;

    protected $fillable = [
        'flow_id',
        'next_step_id',
        'expected_answer_type',
        'question_text',
        'is_start',
    ];

    protected $casts = [
        'expected_answer_type' => FlowStepExpectedAnswerTypeEnum::class,
        'is_start' => 'boolean',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function nextStep()
    {
        return $this->belongsTo(FlowStep::class, 'next_step_id');
    }

    public function answers() {
        return $this->hasMany(Answer::class, 'flow_id');
    }

}
