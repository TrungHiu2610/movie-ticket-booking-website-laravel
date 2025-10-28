<?php

namespace Database\Seeders;

use App\Models\Theater;
use App\Models\Cinema;
use App\Models\SeatType;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class TheaterSeeder extends Seeder
{
    public function run(): void
    {
        $cinemas = Cinema::all();

        if ($cinemas->isEmpty()) {
            $this->command->warn('No cinemas found. Please run CinemaSeeder first.');
            return;
        }

        // Lấy seat types
        $standardSeatType = SeatType::where('name', 'Standard')->first();
        $vipSeatType = SeatType::where('name', 'VIP')->first();

        if (!$standardSeatType || !$vipSeatType) {
            $this->command->warn('Seat types not found. Creating default seat types...');
            $standardSeatType = SeatType::create(['name' => 'Standard', 'surcharge' => 0]);
            $vipSeatType = SeatType::create(['name' => 'VIP', 'surcharge' => 45000]);
        }

        foreach ($cinemas as $cinema) {
            // Tạo 3-5 phòng chiếu cho mỗi rạp
            $theaterCount = rand(3, 5);

            for ($i = 1; $i <= $theaterCount; $i++) {
                $screenType = ['2D', '3D', 'IMAX', '4DX'][array_rand(['2D', '3D', 'IMAX', '4DX'])];

                $theater = Theater::create([
                    'cinema_id' => $cinema->id,
                    'name' => "Phòng {$i}",
                    'screen_type' => $screenType,
                ]);

                // Tạo ghế ngồi (8 hàng x 12 ghế = 96 ghế)
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                $columns = 12;

                foreach ($rows as $row) {
                    for ($col = 1; $col <= $columns; $col++) {
                        // Hàng F, G, H là VIP
                        $isVip = in_array($row, ['F', 'G', 'H']);

                        Seat::create([
                            'theater_id' => $theater->id,
                            'seat_type_id' => $isVip ? $vipSeatType->id : $standardSeatType->id,
                            'row_char' => $row,
                            'column_number' => $col,
                        ]);
                    }
                }

                $this->command->info("Created theater '{$theater->name}' at {$cinema->name} with 96 seats");
            }
        }
    }
}
