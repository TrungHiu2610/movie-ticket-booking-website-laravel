<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    
    public function create($bookingId)
    {
        $booking = Booking::with(['showtime.movie', 'rating'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if (!$booking->is_checked_in) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá sau khi đã xem phim!');
        }

        if ($booking->rating) {
            return redirect()->back()->with('info', 'Bạn đã đánh giá phim này rồi!');
        }

        $movie = $booking->showtime->movie;

        return view('user.ratings.create', compact('booking', 'movie'));
    }

    public function store(Request $request, $bookingId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:1000'
        ]);

        $booking = Booking::where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if (!$booking->is_checked_in) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá sau khi đã xem phim!');
        }

        if ($booking->rating) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá phim này rồi!');
        }

        DB::beginTransaction();
        try {
            Rating::create([
                'user_id' => Auth::id(),
                'movie_id' => $booking->showtime->movie_id,
                'booking_id' => $bookingId,
                'rating' => $request->rating,
                'review' => $request->review
            ]);

            DB::commit();

            return redirect()->route('bookings.history')
                ->with('success', 'Cảm ơn bạn đã đánh giá!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $ratingId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:1000'
        ]);

        $rating = Rating::where('user_id', Auth::id())
            ->findOrFail($ratingId);

        $rating->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return redirect()->back()->with('success', 'Cập nhật đánh giá thành công!');
    }

    public function destroy($ratingId)
    {
        $rating = Rating::where('user_id', Auth::id())
            ->findOrFail($ratingId);

        $rating->delete();

        return redirect()->back()->with('success', 'Đã xóa đánh giá!');
    }
}


