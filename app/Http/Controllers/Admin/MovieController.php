<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovieRequest;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::latest()->paginate(10);


        return Inertia::render('Admin/Movies/Index', [
            'movies' => $movies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();

        return Inertia::render('Admin/Movies/Create', [
            'genres' => $genres,
            'actors' => $actors,
            'directors' => $directors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        $validated = $request->validated();

        $genreIds = $validated['genres'] ?? [];
        $actorIds = $validated['actors'] ?? [];
        $directorIds = $validated['directors'] ?? [];
        unset($validated['genres'], $validated['actors'], $validated['directors']);

        if ($request->hasFile('poster_url')) {
            $path = $request->file('poster_url')->store('posters', config('filesystems.default'));
            $validated['poster_url'] = $path;
        }

        $movie = Movie::create($validated);

        $movie->genres()->sync($genreIds);
        $movie->actors()->sync($actorIds);
        $movie->directors()->sync($directorIds);

        return Redirect::route('movies.index')->with('success', 'Thêm phim thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMovieRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
