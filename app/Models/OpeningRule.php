<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningRule extends Model
{
    protected $fillable = [
        'day_of_week',
        'opens_at',
        'closes_at',
        'slot_duration_minutes',
        'buffer_before',
        'buffer_after',
    ];

}
