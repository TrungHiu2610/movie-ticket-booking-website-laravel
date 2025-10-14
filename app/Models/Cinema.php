<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
    ];
    public function theaters()
    {
        return $this->hasMany(Theater::class);
    }
}
