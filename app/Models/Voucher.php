<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'discount_amount',
        'discount_percentage',
        'max_discount_amount',
        'expires_at',
        'usage_limit',
        'usage_count',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'max_discount_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_voucher');
    }
}
