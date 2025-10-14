<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'showtime_id',
        'total_amount',
        'status',
        'booking_code',
        'qr_code_url',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'booking_voucher');
    }
}
