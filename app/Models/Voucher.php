<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'movie_id',
        'discount_amount',
        'discount_percentage',
        'max_discount_amount',
        'min_purchase_amount',
        'expires_at',
        'usage_limit',
        'usage_count',
        'is_active',
        'description',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'max_discount_amount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_voucher');
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function isValid($movieId = null, $totalAmount = 0)
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Mã giảm giá không còn hiệu lực'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn'];
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng'];
        }

        if ($this->movie_id && $movieId && $this->movie_id != $movieId) {
            return ['valid' => false, 'message' => 'Mã giảm giá không áp dụng cho phim này'];
        }

        if ($this->min_purchase_amount && $totalAmount < $this->min_purchase_amount) {
            return ['valid' => false, 'message' => 'Đơn hàng chưa đủ giá trị tối thiểu: ' . number_format((float)$this->min_purchase_amount) . 'đ'];
        }

        return ['valid' => true, 'message' => 'Mã giảm giá hợp lệ'];
    }

    public function calculateDiscount($totalAmount)
    {
        if ($this->discount_percentage) {
            $discount = ($totalAmount * $this->discount_percentage) / 100;
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
        } else {
            $discount = $this->discount_amount ?? 0;
        }

        return min($discount, $totalAmount);
    }
}
