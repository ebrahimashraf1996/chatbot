<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Flow\FlowStatusEnum;

class Flow extends Model
{
    use HasUuids;

    protected $fillable = [
         'name', 'description', 'status'
    ];

    protected $casts = [
        'status' => FlowStatusEnum::class
    ];



    public function steps()
    {
        return $this->hasMany(FlowStep::class);
    }
}
