<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'seat_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Tính giá vé dựa trên:
     * - Giá cơ bản từ suất chiếu (base_price)
     * - Phụ thu loại ghế (seat_type surcharge)
     * - Phụ thu khác (surcharges: cuối tuần, ngày lễ, 3D, IMAX)
     * 
     * @return float
     */
    public function calculatePrice()
    {
        $showtime = $this->booking->showtime;
        $seat = $this->seat;

        // 1. Giá cơ bản từ suất chiếu
        $basePrice = $showtime->base_price;

        // 2. Phụ thu loại ghế (VIP, Couple, etc.)
        $seatTypeSurcharge = $seat->seatType->surcharge ?? 0;

        // 3. Phụ thu khác (cuối tuần, ngày lễ, 3D, IMAX)
        $additionalSurcharges = $this->calculateApplicableSurcharges($showtime);

        // Tổng giá = base + seat type + surcharges
        return $basePrice + $seatTypeSurcharge + $additionalSurcharges;
    }

    /**
     * Tính tổng phụ thu áp dụng cho vé này
     * Dựa trên ngày chiếu, loại phòng chiếu
     * 
     * @param \App\Models\Showtime $showtime
     * @return float
     */
    protected function calculateApplicableSurcharges($showtime)
    {
        $surcharges = \App\Models\Surcharge::all();
        $total = 0;

        foreach ($surcharges as $surcharge) {
            if ($this->isSurchargeApplicable($surcharge, $showtime)) {
                $total += $surcharge->amount;
            }
        }

        return $total;
    }

    /**
     * Kiểm tra phụ thu có áp dụng không
     * 
     * @param \App\Models\Surcharge $surcharge
     * @param \App\Models\Showtime $showtime
     * @return bool
     */
    protected function isSurchargeApplicable($surcharge, $showtime)
    {
        switch ($surcharge->type) {
            case 'DAY_OF_WEEK':
                // VD: "6,7" cho T7, CN
                $applicableDays = explode(',', $surcharge->apply_condition);
                $showtimeDay = $showtime->start_time->dayOfWeek; // 0=Sun, 6=Sat
                return in_array($showtimeDay, $applicableDays);

            case 'SPECIFIC_DATE':
                // VD: "2025-04-30" cho ngày lễ cụ thể
                $specificDate = $surcharge->apply_condition;
                return $showtime->start_time->format('Y-m-d') === $specificDate;

            case 'SCREEN_TYPE':
                // VD: "3D", "IMAX" - kiểm tra theo tên theater
                $theaterName = $showtime->theater->name;
                return stripos($theaterName, $surcharge->apply_condition) !== false;

            default:
                return false;
        }
    }
}
