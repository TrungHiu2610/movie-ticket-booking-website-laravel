<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'booking_id',
        'processed_by',
        'requested_by',
        'refund_code',
        'original_amount',
        'refund_fee',
        'refund_amount',
        'reason',
        'status',
        'requested_at',
        'processed_at',
        'staff_notes'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'refund_fee' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public static function calculateRefundFee($booking, $originalAmount)
    {
        $showtime = $booking->showtime->start_time;
        $now = now();
        $hoursUntilShowtime = $now->diffInHours($showtime, false);

        // Không cho phép hoàn tiền nếu dưới 2h hoặc đã chiếu
        if ($hoursUntilShowtime < 2) {
            return null; // Không được hoàn
        }

        if ($hoursUntilShowtime >= 24) {
            return 0; // Miễn phí (0%)
        } else {
            return $originalAmount * 0.10; // Từ 2h-24h: 10%
        }
    }

    public static function generateRefundCode()
    {
        return 'RF-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
