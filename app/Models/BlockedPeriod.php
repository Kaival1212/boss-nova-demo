<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedPeriod extends Model
{
    protected $fillable = [
        'starts_at',
        'ends_at',
        'reason',
    ];
}
