<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoyaltyTier;

class LoyaltyTierSeeder extends Seeder
{
    public function run(): void
    {
        LoyaltyTier::create([
            'name' => 'Bạc',
            'min_points' => 0,
            'max_points' => 999,
            'discount_percentage' => 0,
            'color' => '#C0C0C0'
        ]);

        LoyaltyTier::create([
            'name' => 'Vàng',
            'min_points' => 1000,
            'max_points' => 4999,
            'discount_percentage' => 5,
            'color' => '#FFD700'
        ]);

        LoyaltyTier::create([
            'name' => 'Kim Cương',
            'min_points' => 5000,
            'max_points' => null,
            'discount_percentage' => 10,
            'color' => '#B9F2FF'
        ]);
    }
}
