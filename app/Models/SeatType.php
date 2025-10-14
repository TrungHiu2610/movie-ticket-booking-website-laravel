<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surcharge',
    ];

    protected $casts = [
        'surcharge' => 'decimal:2',
    ];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
