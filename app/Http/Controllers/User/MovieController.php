<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::with(['genres', 'actors', 'directors']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($genreId = $request->get('genre')) {
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        if ($actorId = $request->get('actor')) {
            $query->whereHas('actors', function ($q) use ($actorId) {
                $q->where('actors.id', $actorId);
            });
        }

        if ($directorId = $request->get('director')) {
            $query->whereHas('directors', function ($q) use ($directorId) {
                $q->where('directors.id', $directorId);
            });
        }

        $sortBy = $request->get('sort_by', 'release_date');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'release_date':
                $query->orderBy('release_date', $sortOrder);
                break;
            case 'price':
                $query->withMin('showtimes', 'base_price')
                    ->orderBy('showtimes_min_base_price', $sortOrder);
                break;
            default:
                $query->orderBy('release_date', 'desc');
        }

        $movies = $query->paginate(12)->withQueryString();

        $genres = \App\Models\Genre::orderBy('name')->get();
        $actors = \App\Models\Actor::orderBy('name')->limit(50)->get();
        $directors = \App\Models\Director::orderBy('name')->limit(50)->get();

        return view('user.movies.index', compact('movies', 'genres', 'actors', 'directors'));
    }

    public function show(Movie $movie)
    {
        $movie->load(['genres', 'actors', 'directors', 'showtimes.theater.cinema']);

        $showtimes = $movie->showtimes()
            ->with('theater.cinema')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($showtime) {
                return $showtime->start_time->format('Y-m-d');
            });

        $ratings = $movie->ratings()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $movie->averageRating();
        $totalRatings = $movie->ratings()->count();

        $ratingDistribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $count = $movie->ratings()->where('rating', $i)->count();
            $ratingDistribution[$i] = $totalRatings > 0 ? round(($count / $totalRatings) * 100, 1) : 0;
        }

        return view('user.movies.show', compact('movie', 'showtimes', 'ratings', 'averageRating', 'totalRatings', 'ratingDistribution'));
    }
}


