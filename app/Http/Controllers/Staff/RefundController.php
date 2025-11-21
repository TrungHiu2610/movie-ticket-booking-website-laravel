<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Refund;
use App\Models\Seat;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class RefundController extends Controller
{

    public function index()
    {
        $refunds = Refund::with(['booking.user', 'booking.showtime.movie', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.refund.index', compact('refunds'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'search_term' => 'required|string'
        ]);

        $searchTerm = $request->search_term;

        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.theater.cinema', 'tickets.seat', 'refund'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('booking_code', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('tickets.seat', function ($q) use ($searchTerm) {
                        $q->where(DB::raw("CONCAT(row_char, column_number)"), 'LIKE', "%{$searchTerm}%");
                    });
            })
            ->where('status', 'paid')
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vé nào!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'bookings' => $bookings->map(function ($booking) {
                return $this->formatBookingForRefund($booking);
            })
        ]);
    }

    public function show($bookingId)
    {
        $booking = Booking::with(['user', 'showtime.movie', 'showtime.theater.cinema', 'tickets.seat', 'refund'])
            ->findOrFail($bookingId);

        $refundEligibility = $this->checkRefundEligibility($booking);

        // Calculate refund fee
        $refundFee = Refund::calculateRefundFee($booking, $booking->total_amount);

        // If null, cannot refund (less than 2 hours)
        if ($refundFee === null) {
            return response()->json([
                'success' => true,
                'booking' => $this->formatBookingForRefund($booking),
                'refund_info' => [
                    'can_refund' => false,
                    'message' => $refundEligibility['message'],
                    'original_amount' => $booking->total_amount,
                    'refund_fee' => 0,
                    'refund_amount' => 0,
                    'fee_percentage' => 0
                ]
            ]);
        }

        $refundAmount = $booking->total_amount - $refundFee;

        return response()->json([
            'success' => true,
            'booking' => $this->formatBookingForRefund($booking),
            'refund_info' => [
                'can_refund' => $refundEligibility['can_refund'],
                'message' => $refundEligibility['message'],
                'original_amount' => $booking->total_amount,
                'refund_fee' => $refundFee,
                'refund_amount' => $refundAmount,
                'fee_percentage' => $refundFee > 0 ? round(($refundFee / $booking->total_amount) * 100, 1) : 0
            ]
        ]);
    }

    public function process(Request $request, $bookingId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
            'staff_notes' => 'nullable|string|max:1000'
        ]);

        $booking = Booking::with(['user', 'showtime', 'tickets.seat'])->findOrFail($bookingId);

        $eligibility = $this->checkRefundEligibility($booking);
        if (!$eligibility['can_refund']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message']
            ], 400);
        }

        DB::beginTransaction();
        try {
            $refundFee = Refund::calculateRefundFee($booking, $booking->total_amount);

            // Check if refund fee is null (not eligible)
            if ($refundFee === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hoàn tiền trong vòng 2 giờ trước suất chiếu!'
                ], 400);
            }

            $refundAmount = $booking->total_amount - $refundFee;

            $refund = Refund::create([
                'booking_id' => $booking->id,
                'processed_by' => Auth::id(),
                'refund_code' => Refund::generateRefundCode(),
                'original_amount' => $booking->total_amount,
                'refund_fee' => $refundFee,
                'refund_amount' => $refundAmount,
                'reason' => $request->reason,
                'status' => 'completed',
                'requested_at' => now(),
                'processed_at' => now(),
                'staff_notes' => $request->staff_notes
            ]);

            $booking->update([
                'status' => 'cancelled',
                'refund_status' => 'refunded',
                'refunded_at' => now()
            ]);

            foreach ($booking->tickets as $ticket) {
            }

            DB::commit();

            try {
                $emailService = new EmailService();
                $emailService->sendBookingCancellation($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send refund email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Hoàn tiền thành công!',
                'refund' => [
                    'refund_code' => $refund->refund_code,
                    'refund_amount' => number_format((float)$refund->refund_amount, 0, ',', '.') . 'đ'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý hoàn tiền: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkRefundEligibility($booking)
    {
        if ($booking->refund_status === 'refunded') {
            return [
                'can_refund' => false,
                'message' => 'Vé này đã được hoàn tiền rồi!'
            ];
        }

        if ($booking->is_checked_in) {
            return [
                'can_refund' => false,
                'message' => 'Không thể hoàn tiền vé đã soát!'
            ];
        }

        if ($booking->status === 'cancelled') {
            return [
                'can_refund' => false,
                'message' => 'Vé đã bị hủy!'
            ];
        }

        $showtime = $booking->showtime->start_time;
        $hoursUntilShowtime = now()->diffInHours($showtime, false);

        if (now()->gte($showtime)) {
            return [
                'can_refund' => false,
                'message' => 'Không thể hoàn tiền sau khi suất chiếu đã bắt đầu!'
            ];
        }

        if ($hoursUntilShowtime < 2) {
            return [
                'can_refund' => false,
                'message' => 'Không thể hoàn tiền trong vòng 2 giờ trước suất chiếu!'
            ];
        }

        return [
            'can_refund' => true,
            'message' => 'Vé hợp lệ để hoàn tiền'
        ];
    }

    private function formatBookingForRefund($booking)
    {
        $canRefund = $this->checkRefundEligibility($booking);

        return [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'customer_name' => $booking->user->name,
            'customer_email' => $booking->user->email,
            'movie_title' => $booking->showtime->movie->title,
            'cinema_name' => $booking->showtime->theater->cinema->name,
            'theater_name' => $booking->showtime->theater->name,
            'showtime' => $booking->showtime->start_time->format('H:i - d/m/Y'),
            'seats' => $booking->tickets->map(fn($t) => $t->seat->row_char . $t->seat->column_number)->join(', '),
            'total_amount' => number_format($booking->total_amount, 0, ',', '.') . 'đ',
            'status' => $booking->status,
            'refund_status' => $booking->refund_status,
            'is_checked_in' => $booking->is_checked_in,
            'can_refund' => $canRefund['can_refund'],
            'refund_message' => $canRefund['message']
        ];
    }

    public function printReceipt($refundId)
    {
        $refund = Refund::with(['booking.user', 'booking.showtime.movie', 'booking.showtime.theater.cinema', 'booking.tickets.seat', 'processedBy'])
            ->findOrFail($refundId);

        return view('staff.refund.receipt', compact('refund'));
    }

    public function downloadPdf($refundId)
    {
        $refund = Refund::with(['booking.user', 'booking.showtime.movie', 'booking.showtime.theater.cinema', 'booking.tickets.seat', 'processedBy'])
            ->findOrFail($refundId);

        $pdf = Pdf::loadView('staff.refund.receipt', compact('refund'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('refund-' . $refund->refund_code . '.pdf');
    }

    public function scanQR(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string'
        ]);

        $bookingCode = $request->booking_code;

        $booking = Booking::with(['user', 'showtime.movie', 'showtime.theater.cinema', 'tickets.seat', 'refund'])
            ->where('booking_code', $bookingCode)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vé với mã: ' . $bookingCode
            ], 404);
        }

        $refundEligibility = $this->checkRefundEligibility($booking);
        $refundFee = Refund::calculateRefundFee($booking, $booking->total_amount);

        if ($refundFee === null) {
            $refundAmount = 0;
            $feePercentage = 0;
        } else {
            $refundAmount = $booking->total_amount - $refundFee;
            $feePercentage = $refundFee > 0 ? round(($refundFee / $booking->total_amount) * 100, 1) : 0;
        }

        return response()->json([
            'success' => true,
            'booking' => $this->formatBookingForRefund($booking),
            'refund_info' => [
                'can_refund' => $refundEligibility['can_refund'],
                'message' => $refundEligibility['message'],
                'original_amount' => $booking->total_amount,
                'refund_fee' => $refundFee ?? 0,
                'refund_amount' => $refundAmount,
                'fee_percentage' => $feePercentage
            ]
        ]);
    }
}
