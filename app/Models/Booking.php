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

    /**
     * Tính tổng tiền booking
     * = Tổng giá tất cả vé - Giảm giá voucher
     * 
     * @return float
     */
    public function calculateTotal()
    {
        // 1. Tổng giá tất cả vé
        $ticketsTotal = $this->tickets->sum(function ($ticket) {
            return $ticket->calculatePrice();
        });

        // 2. Tính giảm giá từ vouchers
        $discount = $this->calculateVoucherDiscount($ticketsTotal);

        // 3. Tổng cuối = tổng vé - giảm giá
        $total = $ticketsTotal - $discount;

        // Không để âm
        return max(0, $total);
    }

    /**
     * Tính tổng giảm giá từ các voucher áp dụng
     * 
     * @param float $ticketsTotal
     * @return float
     */
    protected function calculateVoucherDiscount($ticketsTotal)
    {
        $totalDiscount = 0;

        foreach ($this->vouchers as $voucher) {
            if ($voucher->discount_percentage) {
                // Giảm theo %
                $discount = ($ticketsTotal * $voucher->discount_percentage) / 100;

                // Áp dụng max discount nếu có
                if ($voucher->max_discount_amount) {
                    $discount = min($discount, $voucher->max_discount_amount);
                }
            } else {
                // Giảm theo số tiền cố định
                $discount = $voucher->discount_amount ?? 0;
            }

            $totalDiscount += $discount;
        }

        return $totalDiscount;
    }

    /**
     * Lưu tổng tiền vào database sau khi tính toán
     * 
     * @return void
     */
    public function updateTotalAmount()
    {
        $this->total_amount = $this->calculateTotal();
        $this->save();
    }

    /**
     * Lấy thông tin chi tiết giá
     * 
     * @return array
     */
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
