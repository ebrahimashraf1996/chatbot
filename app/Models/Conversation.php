<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Conversation\ConversationStatusEnum;

class Conversation extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_phone', 'service_number_id', 'current_step_id', 'status'
    ];

    protected $casts = [
        'status' => ConversationStatusEnum::class
    ];

    public function serviceNumber()
    {
        return $this->belongsTo(ServiceNumber::class);
    }

    public function currentStep()
    {
        return $this->belongsTo(FlowStep::class, 'current_step_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
