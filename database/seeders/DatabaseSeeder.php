<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            GenreSeeder::class,
            ActorSeeder::class,
            DirectorSeeder::class,
            CinemaSeeder::class,
            TheaterSeeder::class,
            MovieSeeder::class,
            ShowtimeSeeder::class,
            SurchargeSeeder::class,
            VoucherSeeder::class,
        ]);
    }
}
