<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Movie::with(['genres', 'actors', 'directors']);

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by age rating
        if ($ageRating = $request->get('age_rating')) {
            $query->where('age_rating', $ageRating);
        }

        // Sorting
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();

        return view('admin.movies.create', compact('genres', 'actors', 'directors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate - Laravel will auto redirect back with errors if validation fails
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'poster_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trailer_url' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'age_rating' => 'required|string',
            'status' => 'required|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'actors' => 'nullable|array',
            'actors.*' => 'exists:actors,id',
            'directors' => 'nullable|array',
            'directors.*' => 'exists:directors,id',
        ], [
            'title.required' => 'Tiêu đề không được để trống',
            'description.required' => 'Mô tả không được để trống',
            'poster_url.required' => 'Vui lòng chọn ảnh poster',
            'poster_url.image' => 'File phải là ảnh',
            'trailer_url.required' => 'URL trailer không được để trống',
            'duration_minutes.required' => 'Thời lượng không được để trống',
            'duration_minutes.integer' => 'Thời lượng phải là số',
            'release_date.required' => 'Ngày phát hành không được để trống',
            'age_rating.required' => 'Độ tuổi không được để trống',
            'status.required' => 'Trạng thái không được để trống',
        ]);

        try {
            $genreIds = $validated['genres'] ?? [];
            $actorIds = $validated['actors'] ?? [];
            $directorIds = $validated['directors'] ?? [];
            unset($validated['genres'], $validated['actors'], $validated['directors']);

            // Upload poster
            if ($request->hasFile('poster_url')) {
                $path = $request->file('poster_url')->store('posters', 'public');
                $validated['poster_url'] = $path;
            }

            // Create movie
            $movie = Movie::create($validated);

            // Sync relationships
            $movie->genres()->sync($genreIds);
            $movie->actors()->sync($actorIds);
            $movie->directors()->sync($directorIds);

            return redirect()->route('admin.movies.index')->with('success', 'Thêm phim thành công!');
        } catch (\Exception $e) {
            Log::error('Store Movie Error:', ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
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
    public function edit(Movie $movie)
    {
        $movie->load(['genres', 'actors', 'directors']);
        $genres = Genre::all();
        $actors = Actor::all();
        $directors = Director::all();

        return view('admin.movies.edit', compact('movie', 'genres', 'actors', 'directors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $validated = $request->validated();

        $genreIds = $validated['genres'] ?? [];
        $actorIds = $validated['actors'] ?? [];
        $directorIds = $validated['directors'] ?? [];
        unset($validated['genres'], $validated['actors'], $validated['directors']);

        // Chỉ xử lý upload nếu có file mới
        if ($request->hasFile('poster_url')) {
            // Xóa file cũ nếu có
            if ($movie->poster_url && Storage::disk(config('filesystems.default'))->exists($movie->poster_url)) {
                Storage::disk(config('filesystems.default'))->delete($movie->poster_url);
            }
            $path = $request->file('poster_url')->store('posters', config('filesystems.default'));
            $validated['poster_url'] = $path;
        } else {
            // Giữ nguyên poster cũ
            unset($validated['poster_url']);
        }

        $movie->update($validated);

        $movie->genres()->sync($genreIds);
        $movie->actors()->sync($actorIds);
        $movie->directors()->sync($directorIds);

        return Redirect::route('admin.movies.index')->with('success', 'Cập nhật phim thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        // Xóa poster file nếu có
        if ($movie->poster_url && Storage::disk(config('filesystems.default'))->exists($movie->poster_url)) {
            Storage::disk(config('filesystems.default'))->delete($movie->poster_url);
        }

        $movie->delete();

        return Redirect::route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }
}
