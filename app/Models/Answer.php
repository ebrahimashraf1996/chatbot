<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasUuids;

    protected $fillable = [
        'flow_step_id', 'answer_value', 'answer_label', 'next_step_id'
    ];

    public function step() {
        return $this->belongsTo(FlowStep::class, 'flow_step_id');
    }

    public function nextStep() {
        return $this->belongsTo(FlowStep::class, 'next_step_id');
    }






}
