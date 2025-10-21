<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::create(['name' => 'Action']);
        Genre::create(['name' => 'Comedy']);
        Genre::create(['name' => 'Drama']);
        Genre::create(['name' => 'Horror']);
        Genre::create(['name' => 'Romance']);
        Genre::create(['name' => 'Sci-Fi']);
        Genre::create(['name' => 'Thriller']);
        Genre::create(['name' => 'Fantasy']);
        Genre::create(['name' => 'Documentary']);
        Genre::create(['name' => 'Animation']);
    }
}
