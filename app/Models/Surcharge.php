<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surcharge extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'type',
        'apply_condition',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
