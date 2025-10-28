<?php

namespace Database\Seeders;

use App\Models\Surcharge;
use Illuminate\Database\Seeder;

class SurchargeSeeder extends Seeder
{
    public function run(): void
    {
        $surcharges = [
            [
                'name' => 'Phụ thu cuối tuần',
                'type' => 'DAY_OF_WEEK',
                'amount' => 20000,
                'apply_condition' => '6,7',
            ],
            [
                'name' => 'Phụ thu ngày lễ 30/4',
                'type' => 'SPECIFIC_DATE',
                'amount' => 30000,
                'apply_condition' => '2025-04-30',
            ],
            [
                'name' => 'Phụ thu phim 3D',
                'type' => 'SCREEN_TYPE',
                'amount' => 40000,
                'apply_condition' => '3D',
            ],
            [
                'name' => 'Phụ thu IMAX',
                'type' => 'SCREEN_TYPE',
                'amount' => 60000,
                'apply_condition' => 'IMAX',
            ],
        ];

        foreach ($surcharges as $surcharge) {
            Surcharge::create($surcharge);
        }

        $this->command->info('Created ' . count($surcharges) . ' surcharges');
    }
}
