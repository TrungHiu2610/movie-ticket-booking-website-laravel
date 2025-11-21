<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Services\FileUploadService;
use App\Events\MovieUpdated;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MovieController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    
    public function index(Request $request)
    {
        $query = Movie::with(['genres', 'actors', 'directors']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($ageRating = $request->get('age_rating')) {
            $query->where('age_rating', $ageRating);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['title', 'base_price', 'release_date', 'duration_minutes', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $movies = $query->paginate(10);

        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();

        return view('admin.movies.create', compact('genres', 'actors', 'directors'));
    }

    public function store(StoreMovieRequest $request)
    {
        try {
            $validated = $request->validated();

            $genreIds = $validated['genres'] ?? [];
            $actorIds = $validated['actors'] ?? [];
            $directorIds = $validated['directors'] ?? [];
            unset($validated['genres'], $validated['actors'], $validated['directors']);

            if ($request->hasFile('poster_url')) {
                $validated['poster_url'] = $this->fileUploadService->uploadPoster($request->file('poster_url'));
            }

            if ($request->hasFile('trailer_url')) {
                $validated['trailer_url'] = $this->fileUploadService->uploadTrailer($request->file('trailer_url'));
            } else {
                $validated['trailer_url'] = $request->input('trailer_url', '');
            }

            $movie = Movie::create($validated);

            $movie->genres()->sync($genreIds);
            $movie->actors()->sync($actorIds);
            $movie->directors()->sync($directorIds);

            event(new MovieUpdated($movie));

            return redirect()->route('admin.movies.index')->with('success', 'Thêm phim thành công!');
        } catch (\Exception $e) {
            Log::error('Store Movie Error:', ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
    }

    public function edit(Movie $movie)
    {
        $movie->load(['genres', 'actors', 'directors']);
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();

        return view('admin.movies.edit', compact('movie', 'genres', 'actors', 'directors'));
    }

    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $validated = $request->validated();

        $genreIds = $validated['genres'] ?? [];
        $actorIds = $validated['actors'] ?? [];
        $directorIds = $validated['directors'] ?? [];
        unset($validated['genres'], $validated['actors'], $validated['directors']);

        if ($request->hasFile('poster_url')) {
            $validated['poster_url'] = $this->fileUploadService->uploadPoster(
                $request->file('poster_url'),
                $movie->poster_url
            );
        } else {
            unset($validated['poster_url']);
        }

        if ($request->hasFile('trailer_url')) {
            $validated['trailer_url'] = $this->fileUploadService->uploadTrailer(
                $request->file('trailer_url'),
                $movie->trailer_url
            );
        } else {
            if ($request->has('trailer_url')) {
                $validated['trailer_url'] = $request->input('trailer_url');
            } else {
                unset($validated['trailer_url']);
            }
        }

        $movie->update($validated);

        $movie->genres()->sync($genreIds);
        $movie->actors()->sync($actorIds);
        $movie->directors()->sync($directorIds);

        event(new MovieUpdated($movie));

        return Redirect::route('admin.movies.index')->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->poster_url) {
            $this->fileUploadService->deleteFromS3($movie->poster_url);
        }

        if ($movie->trailer_url && filter_var($movie->trailer_url, FILTER_VALIDATE_URL) && strpos($movie->trailer_url, 'youtube.com') === false) {
            $this->fileUploadService->deleteFromS3($movie->trailer_url);
        }

        $movie->delete();

        return Redirect::route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }
}


