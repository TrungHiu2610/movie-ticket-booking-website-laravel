<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SeatReservation;
use App\Models\ShowTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function selectSeats($showtimeId)
    {
        $showtime = ShowTime::with(['movie', 'theater.cinema', 'theater.seats.seatType'])
            ->findOrFail($showtimeId);

        if ($showtime->start_time < now()) {
            return redirect()->route('movies.show', $showtime->movie)
                ->with('error', 'Suất chiếu này đã qua. Vui lòng chọn suất chiếu khác.');
        }

        $seats = $showtime->theater->seats()
            ->with('seatType')
            ->orderBy('row_char')
            ->orderBy('column_number')
            ->get();

        $soldSeatIds = DB::table('tickets')
            ->join('bookings', 'tickets.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->whereIn('bookings.status', ['paid', 'confirmed'])
            ->pluck('tickets.seat_id')
            ->toArray();

        $reservations = SeatReservation::where('showtime_id', $showtimeId)
            ->where('expires_at', '>', now())
            ->get()
            ->keyBy('seat_id');

        $myReservations = SeatReservation::where('showtime_id', $showtimeId)
            ->where('user_id', Auth::id())
            ->where('expires_at', '>', now())
            ->pluck('seat_id')
            ->toArray();

        $seats = $seats->map(function ($seat) use ($soldSeatIds, $reservations, $myReservations) {
            $seat->is_sold = in_array($seat->id, $soldSeatIds);
            $reservation = $reservations->get($seat->id);
            $seat->reserved_by_me = in_array($seat->id, $myReservations);
            $seat->is_reserved = !$seat->is_sold && $reservation && $reservation->user_id != Auth::id();
            return $seat;
        });

        return view('user.booking.seats', compact('showtime', 'seats'));
    }

    public function reserveSeats(Request $request)
    {
        DB::reconnect();

        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id'
        ]);

        $showtimeId = $request->showtime_id;
        $seatIds = $request->seat_ids;

        DB::beginTransaction();
        try {
            $showtime = ShowTime::where('id', $showtimeId)->lockForUpdate()->first();

            if (!$showtime) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Suất chiếu không tồn tại hoặc đã có người đặt trước.'
                ], 404);
            }

            SeatReservation::where('showtime_id', $showtimeId)
                ->where('user_id', Auth::id())
                ->delete();
            foreach ($seatIds as $seatId) {
                $seat = DB::table('seats')->where('id', $seatId)->lockForUpdate()->first();

                if (!$seat) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Ghế không tồn tại hoặc đã có người đặt trước.'
                    ], 404);
                }
                $isSold = DB::table('tickets')
                    ->join('bookings', 'tickets.booking_id', '=', 'bookings.id')
                    ->where('tickets.seat_id', $seatId)
                    ->where('bookings.showtime_id', $showtimeId)
                    ->whereIn('bookings.status', ['paid', 'confirmed'])
                    ->lockForUpdate()
                    ->exists();

                if ($isSold) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Ghế đã được đặt bởi người khác. Vui lòng chọn ghế khác.'
                    ], 422);
                }
                $existingReservation = SeatReservation::where('seat_id', $seatId)
                    ->where('showtime_id', $showtimeId)
                    ->where('expires_at', '>', now())
                    ->where('user_id', '!=', Auth::id())
                    ->lockForUpdate()
                    ->first();

                if ($existingReservation) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Ghế đang được giữ bởi người khác. Vui lòng chọn ghế khác.'
                    ], 422);
                }
            }

            $expiresAt = now()->addMinutes(5);
            foreach ($seatIds as $seatId) {
                SeatReservation::create([
                    'seat_id' => $seatId,
                    'showtime_id' => $showtimeId,
                    'user_id' => Auth::id(),
                    'expires_at' => $expiresAt
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ghế đã được giữ trong 5 phút',
                'expires_at' => $expiresAt->toIso8601String()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Seat reservation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function getSeatStatus($showtimeId)
    {
        // Get all seat IDs for this showtime in one query
        $showtime = ShowTime::findOrFail($showtimeId);
        $seats = DB::table('seats')
            ->where('theater_id', $showtime->theater_id)
            ->pluck('id');

        // Get all sold seats in one query
        $soldSeatIds = DB::table('tickets')
            ->join('bookings', 'tickets.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->whereIn('bookings.status', ['paid', 'confirmed'])
            ->pluck('tickets.seat_id')
            ->toArray();

        // Get all active reservations in one query
        $reservations = SeatReservation::where('showtime_id', $showtimeId)
            ->where('expires_at', '>', now())
            ->get()
            ->keyBy('seat_id');

        $status = [];

        foreach ($seats as $seatId) {
            if (in_array($seatId, $soldSeatIds)) {
                $status[$seatId] = 'sold';
                continue;
            }

            $reservation = $reservations->get($seatId);
            if ($reservation) {
                if ($reservation->user_id == Auth::id()) {
                    $status[$seatId] = 'mine';
                } else {
                    $status[$seatId] = 'reserved';
                }
            } else {
                $status[$seatId] = 'available';
            }
        }

        return response()->json([
            'success' => true,
            'status' => $status
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function history()
    {
        $bookings = Booking::with(['tickets.seat', 'showtime.movie', 'showtime.theater.cinema', 'rating'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.booking.history', compact('bookings'));
    }

    public function requestRefund(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền truy cập đặt vé này');
        }

        if (!$booking->canBeRefunded()) {
            return back()->with('error', 'Không thể yêu cầu hoàn tiền cho đặt vé này');
        }

        try {
            $booking->refund()->create([
                'refund_amount' => $booking->total_amount,
                'status' => 'pending',
                'reason' => 'Yêu cầu hoàn tiền từ khách hàng',
                'requested_by' => Auth::id(),
                'requested_at' => now()
            ]);

            return back()->with('success', 'Yêu cầu hoàn tiền đã được gửi. Vui lòng đợi xác nhận.');
        } catch (\Exception $e) {
            Log::error('Refund request error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi gửi yêu cầu hoàn tiền');
        }
    }
}
