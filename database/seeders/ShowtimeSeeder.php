<?php

namespace Database\Seeders;

use App\Models\ShowTime;
use App\Models\Movie;
use App\Models\Theater;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $movies = Movie::all();
        $theaters = Theater::all();

        if ($movies->isEmpty() || $theaters->isEmpty()) {
            $this->command->warn('Please run MovieSeeder and TheaterSeeder first.');
            return;
        }

        // Tạo lịch chiếu cho 7 ngày tới
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(7);

        foreach ($movies as $movie) {
            // Mỗi phim chiếu ở 3-5 rạp ngẫu nhiên
            $selectedTheaters = $theaters->random(rand(3, 5));

            foreach ($selectedTheaters as $theater) {
                // Tạo 3-4 suất chiếu mỗi ngày
                for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                    $showTimes = ['09:00', '14:00', '18:30', '21:00'];
                    $selectedTimes = array_rand(array_flip($showTimes), rand(3, 4));

                    foreach ($selectedTimes as $time) {
                        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $time);
                        $endTime = $startTime->copy()->addMinutes($movie->duration);

                        ShowTime::create([
                            'movie_id' => $movie->id,
                            'theater_id' => $theater->id,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'base_price' => rand(70000, 150000),
                        ]);
                    }
                }
            }

            $this->command->info("Created showtimes for movie: {$movie->title}");
        }
    }
}
