<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\ShowTime;
use App\Models\Theater;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index(Request $request)
    {
        $query = ShowTime::with(['movie', 'theater.cinema']);

        // Search by movie title
        if ($search = $request->get('search')) {
            $query->whereHas('movie', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by movie
        if ($movieId = $request->get('movie_id')) {
            $query->where('movie_id', $movieId);
        }

        // Filter by cinema
        if ($cinemaId = $request->get('cinema_id')) {
            $query->whereHas('theater', function ($q) use ($cinemaId) {
                $q->where('cinema_id', $cinemaId);
            });
        }

        // Filter by date range
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('start_time', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('start_time', '<=', $dateTo);
        }

        // Sorting
        $sortField = $request->get('sort', 'start_time');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['start_time', 'base_price', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('start_time', 'desc');
        }

        $showtimes = $query->paginate(20);

        // Get data for filters
        $movies = Movie::orderBy('title')->pluck('title', 'id');
        $cinemas = Cinema::orderBy('name')->pluck('name', 'id');

        return view('admin.showtimes.index', compact('showtimes', 'movies', 'cinemas'));
    }

    public function create()
    {
        $movies = Movie::where('status', 'now_showing')->orWhere('status', 'coming_soon')->get();
        $cinemas = Cinema::all();
        $theaters = Theater::with('cinema')->get();
        return view('admin.showtimes.create', compact('movies', 'cinemas', 'theaters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'theater_id' => 'required|exists:theaters,id',
            'start_time' => 'required|date|after:now',
            'base_price' => 'required|numeric|min:0',
        ], [
            'movie_id.required' => 'Vui lòng chọn phim',
            'theater_id.required' => 'Vui lòng chọn phòng chiếu',
            'start_time.required' => 'Thời gian bắt đầu không được để trống',
            'start_time.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại',
            'base_price.required' => 'Giá vé không được để trống',
            'base_price.min' => 'Giá vé phải lớn hơn hoặc bằng 0',
        ]);

        $movie = Movie::findOrFail($request->movie_id);
        $startTime = new \DateTime($request->start_time);
        $endTime = clone $startTime;
        $endTime->modify('+' . $movie->duration_minutes . ' minutes');

        ShowTime::create([
            'movie_id' => $request->movie_id,
            'theater_id' => $request->theater_id,
            'start_time' => $request->start_time,
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'base_price' => $request->base_price,
        ]);

        return redirect()->route('admin.showtimes.index')->with('success', 'Thêm suất chiếu thành công!');
    }

    public function edit(ShowTime $showtime)
    {
        $showtime->load(['movie', 'theater.cinema']);
        $movies = Movie::where('status', 'now_showing')->orWhere('status', 'coming_soon')->get();
        $theaters = Theater::with('cinema')->get();
        return view('admin.showtimes.edit', compact('showtime', 'movies', 'theaters'));
    }

    public function update(Request $request, ShowTime $showtime)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'theater_id' => 'required|exists:theaters,id',
            'start_time' => 'required|date',
            'base_price' => 'required|numeric|min:0',
        ], [
            'movie_id.required' => 'Vui lòng chọn phim',
            'theater_id.required' => 'Vui lòng chọn phòng chiếu',
            'start_time.required' => 'Thời gian bắt đầu không được để trống',
            'base_price.required' => 'Giá vé không được để trống',
            'base_price.min' => 'Giá vé phải lớn hơn hoặc bằng 0',
        ]);

        $movie = Movie::findOrFail($request->movie_id);
        $startTime = new \DateTime($request->start_time);
        $endTime = clone $startTime;
        $endTime->modify('+' . $movie->duration_minutes . ' minutes');

        $showtime->update([
            'movie_id' => $request->movie_id,
            'theater_id' => $request->theater_id,
            'start_time' => $request->start_time,
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'base_price' => $request->base_price,
        ]);

        return redirect()->route('admin.showtimes.index')->with('success', 'Cập nhật suất chiếu thành công!');
    }

    public function destroy(ShowTime $showtime)
    {
        $showtime->delete();
        return redirect()->route('admin.showtimes.index')->with('success', 'Xóa suất chiếu thành công!');
    }

    /**
     * Check for schedule conflicts
     */
    public function checkConflicts(Request $request)
    {
        $theaterId = $request->theater_id;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $excludeId = $request->exclude_id; // For edit mode

        $conflicts = ShowTime::with('movie')
            ->where('theater_id', $theaterId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $conflicts->where('id', '!=', $excludeId);
        }

        $conflicts = $conflicts->get()->map(function ($showtime) {
            return [
                'id' => $showtime->id,
                'movie_title' => $showtime->movie->title,
                'start_time' => $showtime->start_time->toISOString(),
                'end_time' => $showtime->end_time->toISOString(),
            ];
        });

        return response()->json([
            'conflicts' => $conflicts,
            'has_conflicts' => $conflicts->count() > 0,
        ]);
    }
}
