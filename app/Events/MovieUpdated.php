<?php

namespace App\Events;

use App\Models\Movie;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MovieUpdated
{
    use Dispatchable, SerializesModels;

    public $movie;

    /**
     * Create a new event instance.
     */
    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }
}
