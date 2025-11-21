<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::truncate();

        $movies = Movie::take(2)->get();

        $vouchers = [
            [
                'code' => 'WELCOME2024',
                'movie_id' => null,
                'discount_percentage' => 20,
                'max_discount_amount' => 50000,
                'min_purchase_amount' => 100000,
                'usage_limit' => 100,
                'usage_count' => 15,
                'is_active' => DB::raw('true'),
                'description' => 'Giảm 20% cho đơn hàng từ 100k - Áp dụng tất cả phim',
                'expires_at' => Carbon::now()->addMonths(2),
            ],
            [
                'code' => 'WEEKEND50K',
                'movie_id' => null,
                'discount_amount' => 50000,
                'max_discount_amount' => 50000,
                'min_purchase_amount' => 200000,
                'usage_limit' => 50,
                'usage_count' => 23,
                'is_active' => DB::raw('true'),
                'description' => 'Giảm 50k cho đơn từ 200k - Cuối tuần',
                'expires_at' => Carbon::now()->addMonth(),
            ],
            [
                'code' => 'NEWYEAR2025',
                'movie_id' => null,
                'discount_percentage' => 30,
                'max_discount_amount' => 100000,
                'min_purchase_amount' => 150000,
                'usage_limit' => 1000,
                'usage_count' => 0,
                'is_active' => DB::raw('true'),
                'description' => 'Giảm 30% tối đa 100k - Chào năm mới',
                'expires_at' => Carbon::now()->addMonths(4),
            ],
        ];

        if ($movies->count() >= 2) {
            $vouchers[] = [
                'code' => 'OPPENHEIMER20',
                'movie_id' => $movies[0]->id,
                'discount_percentage' => 20,
                'max_discount_amount' => 40000,
                'min_purchase_amount' => 80000,
                'usage_limit' => 200,
                'usage_count' => 0,
                'is_active' => DB::raw('true'),
                'description' => 'Giảm 20% cho phim ' . $movies[0]->title,
                'expires_at' => Carbon::now()->addMonth(),
            ];

            $vouchers[] = [
                'code' => 'BARBIE15',
                'movie_id' => $movies[1]->id,
                'discount_percentage' => 15,
                'max_discount_amount' => 30000,
                'min_purchase_amount' => 50000,
                'usage_limit' => 150,
                'usage_count' => 0,
                'is_active' => DB::raw('true'),
                'description' => 'Giảm 15% cho phim ' . $movies[1]->title,
                'expires_at' => Carbon::now()->addMonth(),
            ];
        }

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }

        $this->command->info('Created ' . count($vouchers) . ' vouchers');
    }
}
