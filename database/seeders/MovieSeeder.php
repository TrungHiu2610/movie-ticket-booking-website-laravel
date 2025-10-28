<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();

        if ($genres->isEmpty() || $actors->isEmpty() || $directors->isEmpty()) {
            $this->command->warn('Please run GenreSeeder, ActorSeeder, and DirectorSeeder first.');
            return;
        }

        $movies = [
            [
                'title' => 'Oppenheimer',
                'description' => 'Câu chuyện về J. Robert Oppenheimer, người đứng đầu dự án Manhattan và phát triển bom nguyên tử.',
                'duration_minutes' => 180,
                'base_price' => 120000, // Phim dài, chất lượng cao
                'release_date' => '2024-01-15',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/oppenheimer.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=trailer1',
                'age_rating' => 'C16',
                'status' => 'now_showing',
            ],
            [
                'title' => 'Barbie',
                'description' => 'Barbie và Ken khám phá thế giới thực sau khi bị trục xuất khỏi Barbieland.',
                'duration_minutes' => 114,
                'base_price' => 100000, // Phim gia đình
                'release_date' => '2024-02-20',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/barbie.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=trailer2',
                'age_rating' => 'P',
                'status' => 'now_showing',
            ],
            [
                'title' => 'Godzilla x Kong: The New Empire',
                'description' => 'Kong và Godzilla phải đối mặt với một mối đe dọa mới từ lòng đất.',
                'duration_minutes' => 115,
                'base_price' => 110000, // Phim hành động blockbuster
                'release_date' => '2024-03-10',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/godzilla.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=trailer3',
                'age_rating' => 'C13',
                'status' => 'now_showing',
            ],
            [
                'title' => 'Dune: Part Two',
                'description' => 'Paul Atreides hợp nhất với Chani và Fremen trong cuộc chiến trả thù.',
                'duration_minutes' => 166,
                'base_price' => 130000, // Bom tấn sci-fi cao cấp
                'release_date' => '2024-04-05',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/dune2.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=trailer4',
                'age_rating' => 'C16',
                'status' => 'now_showing',
            ],
            [
                'title' => 'Deadpool & Wolverine',
                'description' => 'Deadpool hợp tác với Wolverine trong một nhiệm vụ đổi đời.',
                'duration_minutes' => 127,
                'base_price' => 115000, // Phim Marvel hot
                'release_date' => '2024-05-18',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/deadpool3.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=trailer5',
                'age_rating' => 'C18',
                'status' => 'coming_soon',
            ],
        ];

        foreach ($movies as $movieData) {
            $movie = Movie::create($movieData);

            // Gắn genres ngẫu nhiên (2-3 genres)
            $movie->genres()->attach(
                $genres->random(rand(2, 3))->pluck('id')->toArray()
            );

            // Gắn actors ngẫu nhiên (3-5 actors)
            $movie->actors()->attach(
                $actors->random(rand(3, 5))->pluck('id')->toArray()
            );

            // Gắn director ngẫu nhiên (1 director)
            $movie->directors()->attach(
                $directors->random(1)->pluck('id')->toArray()
            );

            $this->command->info("Created movie: {$movie->title}");
        }
    }
}
