<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'theater_id',
        'seat_type_id',
        'row_char',
        'column_number',
    ];

    protected $casts = [
        'column_number' => 'integer',
    ];


    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }
}
