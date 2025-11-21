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

    public function calculatePrice()
    {
        $showtime = $this->booking->showtime;
        $seat = $this->seat;
        $basePrice = $showtime->base_price;
        $seatTypeSurcharge = $seat->seatType->surcharge ?? 0;
        $additionalSurcharges = $this->calculateApplicableSurcharges($showtime);
        $totalPrice = $basePrice + $seatTypeSurcharge + $additionalSurcharges;
        return $this->roundUpToThousand($totalPrice);
    }

    protected function roundUpToThousand($price)
    {
        return ceil($price / 1000) * 1000;
    }

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

    protected function isSurchargeApplicable($surcharge, $showtime)
    {
        switch ($surcharge->type) {
            case 'DAY_OF_WEEK':
                $applicableDays = explode(',', $surcharge->apply_condition);
                $showtimeDay = $showtime->start_time->dayOfWeek; // 0=Sun, 6=Sat
                return in_array($showtimeDay, $applicableDays);

            case 'SPECIFIC_DATE':
                $specificDate = $surcharge->apply_condition;
                return $showtime->start_time->format('Y-m-d') === $specificDate;

            case 'SCREEN_TYPE':
                $theaterName = $showtime->theater->name;
                return stripos($theaterName, $surcharge->apply_condition) !== false;

            default:
                return false;
        }
    }
}


