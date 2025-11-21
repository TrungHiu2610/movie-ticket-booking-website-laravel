<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketScannerController extends Controller
{

    public function index()
    {
        return view('staff.scanner.index');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string'
        ]);

        $booking = Booking::with(['user', 'showtime.theater.cinema', 'showtime.movie', 'tickets.seat'])
            ->where('booking_code', $request->booking_code)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vé với mã: ' . $request->booking_code
            ], 404);
        }

        if ($booking->is_checked_in) {
            return response()->json([
                'success' => true,
                'booking' => $this->formatBookingData($booking),
                'message' => 'Vé này đã được soát rồi!'
            ]);
        }

        $showtime = $booking->showtime->start_time;
        $now = now();
        $earliestCheckIn = $showtime->copy()->subMinutes(30);
        $latestCheckIn = $showtime->copy()->addHours(3);

        $canCheckIn = $now->between($earliestCheckIn, $latestCheckIn);
        $message = null;

        if (!$canCheckIn) {
            if ($now->lt($earliestCheckIn)) {
                $message = 'Chưa đến thời gian soát vé. Có thể soát từ ' . $earliestCheckIn->format('H:i d/m/Y');
            } else {
                $message = 'Đã quá thời gian soát vé (sau 3 giờ kể từ giờ chiếu)';
            }
        }

        return response()->json([
            'success' => true,
            'booking' => $this->formatBookingData($booking, $canCheckIn, $message)
        ]);
    }

    private function formatBookingData($booking, $canCheckIn = false, $message = null)
    {
        return [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'user_name' => $booking->user->name,
            'movie_title' => $booking->showtime->movie->title,
            'cinema_name' => $booking->showtime->theater->cinema->name,
            'theater_name' => $booking->showtime->theater->name,
            'showtime' => $booking->showtime->start_time->format('H:i - d/m/Y'),
            'tickets' => $booking->tickets->map(function ($ticket) {
                return [
                    'seat_name' => $ticket->seat->row_char . $ticket->seat->column_number
                ];
            }),
            'total_amount' => number_format($booking->total_amount, 0, ',', '.') . 'đ',
            'is_checked_in' => $booking->is_checked_in,
            'checked_in_at' => $booking->checked_in_at ? $booking->checked_in_at->format('H:i - d/m/Y') : null,
            'checked_in_by' => $booking->checkedInBy ? $booking->checkedInBy->name : null,
            'can_check_in' => $canCheckIn,
            'message' => $message
        ];
    }

    public function checkIn(Request $request, $booking_id = null)
    {
        // Accept booking_id from URL parameter or request body
        $bookingId = $booking_id ?? $request->input('booking_id');

        if (!$bookingId) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ID is required'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $booking = Booking::with(['user', 'showtime.theater.cinema', 'showtime.movie', 'tickets.seat', 'checkedInBy'])
                ->findOrFail($bookingId);

            if ($booking->is_checked_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vé đã được soát!'
                ], 400);
            }

            $showtime = $booking->showtime->start_time;
            $now = now();
            $earliestCheckIn = $showtime->copy()->subMinutes(30);
            $latestCheckIn = $showtime->copy()->addHours(3);

            if (!$now->between($earliestCheckIn, $latestCheckIn)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể soát vé ngoài thời gian cho phép'
                ], 400);
            }

            $booking->update([
                'is_checked_in' => DB::raw('t'),
                'checked_in_at' => now(),
                'checked_in_by' => Auth::id()
            ]);

            DB::commit();

            $booking->load('checkedInBy'); // Reload to get staff info

            return response()->json([
                'success' => true,
                'message' => 'Soát vé thành công!',
                'booking' => $this->formatBookingData($booking)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        $bookings = Booking::with(['user', 'showtime.movie', 'checkedInBy'])
            ->where('checked_in_by', Auth::id())
            ->orderBy('checked_in_at', 'desc')
            ->paginate(20);

        return view('staff.scanner.history', compact('bookings'));
    }
}
