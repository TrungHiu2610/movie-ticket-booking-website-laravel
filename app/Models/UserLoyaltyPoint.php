<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoyaltyPoint extends Model
{
    protected $fillable = [
        'user_id',
        'total_points',
        'current_tier_id',
    ];

    protected $casts = [
        'total_points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentTier()
    {
        return $this->belongsTo(LoyaltyTier::class, 'current_tier_id');
    }

    public function transactions()
    {
        return $this->hasMany(PointTransaction::class, 'user_id', 'user_id');
    }

    public function addPoints($points, $type, $description, $bookingId = null)
    {
        $this->total_points += $points;
        $this->updateTier();
        $this->save();

        PointTransaction::create([
            'user_id' => $this->user_id,
            'booking_id' => $bookingId,
            'points' => $points,
            'type' => $type,
            'description' => $description,
        ]);

        return $this;
    }

    public function updateTier()
    {
        $tier = LoyaltyTier::getTierForPoints($this->total_points);
        if ($tier) {
            $this->current_tier_id = $tier->id;
        }
    }
}
