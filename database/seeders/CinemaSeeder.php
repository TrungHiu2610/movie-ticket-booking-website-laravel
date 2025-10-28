<?php

namespace Database\Seeders;

use App\Models\Cinema;
use Illuminate\Database\Seeder;

class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $cinemas = [
            [
                'name' => 'CGV Vincom Bà Triệu',
                'address' => '191 Bà Triệu, Quận Hai Bà Trưng',
                'city' => 'Hà Nội',
            ],
            [
                'name' => 'CGV Aeon Long Biên',
                'address' => '27 Cổ Linh, Quận Long Biên',
                'city' => 'Hà Nội',
            ],
            [
                'name' => 'Lotte Cinema Landmark 81',
                'address' => '720A Điện Biên Phủ, Quận Bình Thạnh',
                'city' => 'TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Galaxy Nguyễn Du',
                'address' => '116 Nguyễn Du, Quận 1',
                'city' => 'TP. Hồ Chí Minh',
            ],
            [
                'name' => 'BHD Star Vincom Đà Nẵng',
                'address' => '910A Ngô Quyền, Quận Sơn Trà',
                'city' => 'Đà Nẵng',
            ],
        ];

        foreach ($cinemas as $cinema) {
            Cinema::create($cinema);
        }
    }
}
