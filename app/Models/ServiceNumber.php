<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceNumber\ServiceNumberStatusEnum;

class ServiceNumber extends Model
{
    use HasUuids;

    protected $fillable = [
        'flow_id', 'name', 'phone_number', 'twilio_sid', 'twilio_token', 'status'
    ];

    protected $casts = [
        'status' => ServiceNumberStatusEnum::class
    ];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function flow() {
        return $this->belongsTo(Flow::class, 'flow_id');
    }
}
