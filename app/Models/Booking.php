<?php

namespace App\Models;

use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

#[ObservedBy(BookingObserver::class)]
class Booking extends Model implements \Illuminate\Contracts\Queue\ShouldQueue
{
    // use Queueable;

    protected $fillable = [
        'client_id',
        'date',
        'status',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
