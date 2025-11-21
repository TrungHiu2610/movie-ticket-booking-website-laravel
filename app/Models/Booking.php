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
        'is_checked_in',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function setIsCheckedInAttribute($value)
    {
        $this->attributes['is_checked_in'] = (bool) $value;
    }

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

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function isCheckedIn()
    {
        return $this->is_checked_in;
    }

    public function canBeRated()
    {
        if (!$this->is_checked_in || $this->rating) {
            return false;
        }

        $showtimeEndTime = $this->showtime->start_time->copy()
            ->addMinutes($this->showtime->movie->duration_minutes);

        $canRateTime = $showtimeEndTime->copy();

        return now()->addHours(7)->greaterThanOrEqualTo($canRateTime);
    }

    public function canBeRefunded()
    {
        if ($this->status !== 'confirmed' || $this->refund || $this->is_checked_in) {
            return false;
        }

        $showtimeStart = $this->showtime->start_time;
        $minRefundTime = $showtimeStart->copy()->subHours(24);

        return now()->lessThan($showtimeStart) && now()->lessThan($minRefundTime);
    }

    public function calculateTotal()
    {
        $ticketsTotal = $this->tickets->sum(function ($ticket) {
            return $ticket->calculatePrice();
        });
        $discount = $this->calculateVoucherDiscount($ticketsTotal);
        $total = $ticketsTotal - $discount;
        return max(0, $total);
    }

    protected function calculateVoucherDiscount($ticketsTotal)
    {
        $totalDiscount = 0;

        foreach ($this->vouchers as $voucher) {
            if ($voucher->discount_percentage) {
                $discount = ($ticketsTotal * $voucher->discount_percentage) / 100;
                if ($voucher->max_discount_amount) {
                    $discount = min($discount, $voucher->max_discount_amount);
                }
            } else {
                $discount = $voucher->discount_amount ?? 0;
            }

            $totalDiscount += $discount;
        }

        return $totalDiscount;
    }

    public function updateTotalAmount()
    {
        $total = $this->calculateTotal();
        $this->total_amount = round($total, 2);
        $this->save();
    }

    public function getPriceBreakdown()
    {
        $ticketsTotal = $this->tickets->sum(function ($ticket) {
            return $ticket->calculatePrice();
        });

        $discount = $this->calculateVoucherDiscount($ticketsTotal);

        return [
            'tickets_total' => $ticketsTotal,
            'discount' => $discount,
            'final_total' => max(0, $ticketsTotal - $discount),
            'tickets_count' => $this->tickets->count(),
        ];
    }
}
