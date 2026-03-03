<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'color',
        'customer_name',
        'customer_phone',
        'payment_status',
        'event_type',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
