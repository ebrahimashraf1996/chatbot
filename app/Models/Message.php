<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasUuids;

    protected $fillable = [
        'conversation_id', 'step_id', 'user_message', 'bot_response'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function step()
    {
        return $this->belongsTo(FlowStep::class, 'step_id');
    }
}
