<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTier extends Model
{
    protected $fillable = [
        'name',
        'min_points',
        'max_points',
        'discount_percentage',
        'color',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
        'discount_percentage' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(UserLoyaltyPoint::class, 'current_tier_id');
    }

    public static function getTierForPoints($points)
    {
        return self::where('min_points', '<=', $points)
            ->where(function ($query) use ($points) {
                $query->whereNull('max_points')
                    ->orWhere('max_points', '>=', $points);
            })
            ->orderBy('min_points', 'desc')
            ->first();
    }
}
