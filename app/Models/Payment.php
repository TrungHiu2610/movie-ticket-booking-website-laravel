<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'payment_time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
