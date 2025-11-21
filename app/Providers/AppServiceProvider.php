<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\MovieUpdated;
use App\Listeners\EmbedMovieListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Register event listeners
        Event::listen(
            MovieUpdated::class,
            EmbedMovieListener::class
        );
    }
}
