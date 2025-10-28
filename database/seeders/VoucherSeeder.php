<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'WELCOME2024',
                'discount_percentage' => 20,
                'max_discount_amount' => 50000,
                'usage_limit' => 100,
                'usage_count' => 15,
                'expires_at' => Carbon::now()->addMonths(2),
            ],
            [
                'code' => 'WEEKEND50K',
                'discount_amount' => 50000,
                'max_discount_amount' => 50000,
                'usage_limit' => 50,
                'usage_count' => 23,
                'expires_at' => Carbon::now()->addMonth(),
            ],
            [
                'code' => 'FREESHIP',
                'discount_amount' => 30000,
                'max_discount_amount' => 30000,
                'usage_limit' => 200,
                'usage_count' => 87,
                'expires_at' => Carbon::now()->addMonths(3),
            ],
            [
                'code' => 'NEWYEAR2025',
                'discount_percentage' => 30,
                'max_discount_amount' => 100000,
                'usage_limit' => 1000,
                'usage_count' => 0,
                'expires_at' => Carbon::now()->addMonths(4),
            ],
            [
                'code' => 'EXPIRED2023',
                'discount_percentage' => 25,
                'max_discount_amount' => 75000,
                'usage_limit' => 50,
                'usage_count' => 50,
                'expires_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }

        $this->command->info('Created ' . count($vouchers) . ' vouchers');
    }
}
