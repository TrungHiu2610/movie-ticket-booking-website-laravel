<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\SeatReservation;
use App\Models\ShowTime;
use App\Models\Ticket;
use App\Models\UserLoyaltyPoint;
use App\Models\LoyaltyTier;
use App\Services\QRCodeService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private $vnpUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    private $vnpTmnCode = 'D9BL7VZ1';
    private $vnpHashSecret = '3VJ6GWTIN123XLGKOXJV1VHLIYX2M5VN';
    private $vnpReturnUrl;

    private $useMockPayment = false;

    public function __construct()
    {
        // Use ngrok URL if available (for VNPay callback during local development)
        $baseUrl = env('NGROK_URL') ?: env('APP_URL');
        $this->vnpReturnUrl = $baseUrl . '/payment/callback';
    }

    public function showPaymentPage(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|json'
        ]);

        $showtimeId = $request->showtime_id;
        $seatIds = json_decode($request->seat_ids);

        $showtime = ShowTime::with(['movie', 'theater.cinema'])->findOrFail($showtimeId);

        $seats = DB::table('seats')
            ->join('seat_types', 'seats.seat_type_id', '=', 'seat_types.id')
            ->whereIn('seats.id', $seatIds)
            ->select('seats.*', 'seat_types.name as type_name', 'seat_types.surcharge')
            ->get();

        $totalAmount = 0;
        $seatDetails = [];

        foreach ($seats as $seat) {
            $seatPrice = $showtime->base_price + $seat->surcharge;
            $totalAmount += $seatPrice;

            $seatDetails[] = [
                'id' => $seat->id,
                'name' => $seat->row_char . $seat->column_number,
                'type' => $seat->type_name,
                'price' => $seatPrice
            ];
        }

        $loyaltyDiscount = 0;
        $userLoyalty = UserLoyaltyPoint::where('user_id', Auth::id())->first();
        if ($userLoyalty && $userLoyalty->currentTier) {
            $loyaltyDiscount = ($totalAmount * $userLoyalty->currentTier->discount_percentage) / 100;
            $totalAmount = $totalAmount - $loyaltyDiscount;
        }

        $paymentData = [
            'showtime_id' => $showtimeId,
            'seat_ids' => $seatIds,
            'seat_details' => $seatDetails,
            'total_amount' => $totalAmount,
            'loyalty_discount' => $loyaltyDiscount
        ];

        session(['payment_data' => $paymentData]);

        // Also store in Redis for VNPay callback (session might be lost)
        $sessionId = session()->getId();
        \Illuminate\Support\Facades\Redis::setex(
            "payment_session:{$sessionId}",
            7200, // 2 hours
            json_encode($paymentData)
        );

        return view('user.booking.payment', compact('showtime', 'seatDetails', 'totalAmount'));
    }

    public function createPayment(Request $request)
    {
        DB::reconnect();

        $paymentData = session('payment_data');

        if (!$paymentData) {
            return redirect()->route('home')->with('error', 'Phiên đặt vé đã hết hạn');
        }

        $voucherCode = $request->input('voucher_code');
        $voucherDiscount = 0;
        $voucherId = null;

        if ($voucherCode) {
            $voucher = \App\Models\Voucher::where('code', $voucherCode)->first();
            if ($voucher) {
                $showtime = ShowTime::findOrFail($paymentData['showtime_id']);
                $totalBeforeDiscount = array_sum(array_column($paymentData['seat_details'], 'price'));
                $validation = $voucher->isValid($showtime->movie_id, $totalBeforeDiscount);

                if ($validation['valid']) {
                    $voucherDiscount = $voucher->calculateDiscount($totalBeforeDiscount);
                    $voucherId = $voucher->id;
                }
            }
        }

        $paymentData['voucher_id'] = $voucherId;
        $paymentData['voucher_discount'] = $voucherDiscount;
        session(['payment_data' => $paymentData]);

        $orderId = 'UNICINE_' . time();
        $loyaltyDiscount = $paymentData['loyalty_discount'] ?? 0;
        $totalBeforeDiscount = array_sum(array_column($paymentData['seat_details'], 'price'));
        $amount = max(0, $totalBeforeDiscount - $loyaltyDiscount - $voucherDiscount);

        if ($this->useMockPayment) {
            return $this->createMockPayment($orderId, $amount, $paymentData);
        }

        $vnpTxnRef = $orderId;
        $vnpOrderInfo = "Thanh toan ve xem phim UniCine";
        $vnpOrderType = 'billpayment';
        $vnpAmount = $amount * 100; // VNPay requires amount in VND * 100
        $vnpLocale = 'vn';
        $vnpBankCode = '';
        $vnpIpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnpTmnCode,
            "vnp_Amount" => $vnpAmount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnpIpAddr,
            "vnp_Locale" => $vnpLocale,
            "vnp_OrderInfo" => $vnpOrderInfo,
            "vnp_OrderType" => $vnpOrderType,
            "vnp_ReturnUrl" => $this->vnpReturnUrl,
            "vnp_TxnRef" => $vnpTxnRef,
        ];

        if ($vnpBankCode) {
            $inputData['vnp_BankCode'] = $vnpBankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpUrl = $this->vnpUrl . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnpHashSecret);
        $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;

        $payment = Payment::create([
            'booking_id' => null,
            'amount' => $amount,
            'payment_method' => 'vnpay',
            'transaction_id' => $vnpTxnRef,
            'status' => 'pending',
            'payment_time' => now(),
            'payment_data' => json_encode($inputData)
        ]);

        session(['payment_id' => $payment->id]);

        // Store payment_data in Redis with transaction_id as key for callback
        \Illuminate\Support\Facades\Redis::setex(
            "vnpay_payment:{$vnpTxnRef}",
            3600, // 1 hour
            json_encode($paymentData)
        );

        return redirect($vnpUrl);
    }

    public function callback(Request $request)
    {
        DB::reconnect();

        $vnpTxnRef = $request->vnp_TxnRef;
        $vnpResponseCode = $request->vnp_ResponseCode;
        $vnpSecureHash = $request->vnp_SecureHash;

        $payment = Payment::where('transaction_id', $vnpTxnRef)->first();

        if (!$payment) {
            return $this->handleMockPayment($request);
        }

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnpHashSecret);

        if ($secureHash !== $vnpSecureHash) {
            Log::error('Invalid VNPay signature', ['expected' => $secureHash, 'received' => $vnpSecureHash]);
            return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ');
        }

        if ($vnpResponseCode == '00') {
            return $this->handleSuccessfulPayment($payment, $request);
        } else {
            $payment->update([
                'status' => 'failed',
                'payment_time' => now(),
                'payment_data' => json_encode($request->all())
            ]);

            return redirect()->route('home')->with('error', 'Thanh toán không thành công. Mã lỗi: ' . $vnpResponseCode);
        }
    }

    private function handleSuccessfulPayment($payment, $request)
    {
        DB::beginTransaction();
        try {
            $extraData = json_decode(base64_decode($request->extraData), true);
            $paymentData = session('payment_data');

            // If session is lost, try to get from Redis using transaction_id
            if (!$paymentData) {
                $redisKey = "vnpay_payment:{$payment->transaction_id}";
                $cachedData = \Illuminate\Support\Facades\Redis::get($redisKey);

                if ($cachedData) {
                    $paymentData = json_decode($cachedData, true);
                } else {
                    throw new \Exception('Payment data not found. Session expired.');
                }
            }
            foreach ($paymentData['seat_ids'] as $seatId) {
                $seat = DB::table('seats')->where('id', $seatId)->lockForUpdate()->first();

                if (!$seat) {
                    throw new \Exception('Ghế không tồn tại hoặc đã có người đặt trước');
                }
                $isSold = DB::table('tickets')
                    ->join('bookings', 'tickets.booking_id', '=', 'bookings.id')
                    ->where('tickets.seat_id', $seatId)
                    ->where('bookings.showtime_id', $paymentData['showtime_id'])
                    ->whereIn('bookings.status', ['paid', 'confirmed'])
                    ->lockForUpdate()
                    ->exists();

                if ($isSold) {
                    throw new \Exception('Ghế ' . $seat->row_char . $seat->column_number . ' đã được đặt bởi người khác. Vui lòng chọn ghế khác.');
                }

                $hasValidReservation = SeatReservation::where('seat_id', $seatId)
                    ->where('showtime_id', $paymentData['showtime_id'])
                    ->where('user_id', Auth::id())
                    ->where('expires_at', '>', now())
                    ->lockForUpdate()
                    ->exists();

                if (!$hasValidReservation) {
                    throw new \Exception('Ghế ' . $seat->row_char . $seat->column_number . ' không còn được giữ cho bạn. Vui lòng đặt lại.');
                }
            }

            $qrService = new QRCodeService();
            $emailService = new EmailService();

            $bookingCode = $qrService->generateBookingCode();

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $paymentData['showtime_id'],
                'booking_code' => $bookingCode,
                'total_amount' => $paymentData['total_amount'],
                'status' => 'paid',
            ]);

            $qrCodeUrl = $qrService->generateBookingQRCode($bookingCode);
            $booking->update(['qr_code_url' => $qrCodeUrl]);

            foreach ($paymentData['seat_details'] as $seatDetail) {
                Ticket::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seatDetail['id'],
                    'price' => $seatDetail['price']
                ]);
            }

            if (isset($paymentData['voucher_id']) && $paymentData['voucher_id']) {
                $voucher = \App\Models\Voucher::find($paymentData['voucher_id']);
                if ($voucher) {
                    $booking->vouchers()->attach($voucher->id);
                    $voucher->increment('usage_count');
                }
            }

            $payment->update([
                'booking_id' => $booking->id,
                'status' => 'completed',
                'payment_time' => now(),
                'payment_data' => json_encode($request->all())
            ]);

            SeatReservation::where('user_id', Auth::id())
                ->where('showtime_id', $paymentData['showtime_id'])
                ->whereIn('seat_id', $paymentData['seat_ids'])
                ->delete();

            DB::commit();

            try {
                $this->awardLoyaltyPoints(Auth::id(), $booking);
            } catch (\Exception $e) {
                Log::error('Failed to award loyalty points: ' . $e->getMessage());
            }

            try {
                $emailService->sendBookingConfirmation($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send booking email: ' . $e->getMessage());
            }

            session()->forget(['payment_data', 'payment_id']);

            return redirect()->route('booking.success', $booking->id)
                ->with('success', 'Đặt vé thành công! Vui lòng kiểm tra email để nhận mã QR.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            if (strpos($e->getMessage(), 'đã được đặt') !== false || strpos($e->getMessage(), 'không còn được giữ') !== false) {
                session()->forget(['payment_data', 'payment_id']);
                return redirect()->route('home')->with('error', $e->getMessage());
            }

            return redirect()->route('home')->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
        }
    }

    public function ipn(Request $request)
    {
        Log::info('VNPay IPN received', $request->all());

        $vnpResponseCode = $request->vnp_ResponseCode;
        $vnpTxnRef = $request->vnp_TxnRef;

        if ($vnpResponseCode == '00') {
            return response()->json(['RspCode' => '00', 'Message' => 'Success'], 200);
        }

        return response()->json(['RspCode' => '99', 'Message' => 'Failed'], 400);
    }

    public function success($bookingId)
    {
        $booking = Booking::with(['tickets.seat', 'showtime.movie', 'showtime.theater.cinema'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        return view('user.booking.success', compact('booking'));
    }

    private function createMockPayment($orderId, $amount, $paymentData)
    {
        return view('user.booking.mock-payment', compact('orderId', 'amount', 'paymentData'));
    }

    private function handleMockPayment(Request $request)
    {
        DB::reconnect();

        $resultCode = $request->resultCode;
        $orderId = $request->orderId;

        $paymentData = session('payment_data');

        // Fallback to Redis if session lost
        if (!$paymentData) {
            $sessionId = session()->getId();
            $cachedData = \Illuminate\Support\Facades\Redis::get("payment_session:{$sessionId}");
            if ($cachedData) {
                $paymentData = json_decode($cachedData, true);
            }
        }

        if (!$paymentData) {
            return redirect()->route('home')->with('error', 'Phiên đặt vé đã hết hạn');
        }

        if ($resultCode != 0) {
            SeatReservation::where('user_id', Auth::id())
                ->where('showtime_id', $paymentData['showtime_id'])
                ->whereIn('seat_id', $paymentData['seat_ids'])
                ->delete();

            session()->forget(['payment_data', 'payment_id']);

            // Clean up Redis
            $sessionId = session()->getId();
            \Illuminate\Support\Facades\Redis::del("payment_session:{$sessionId}");

            return redirect()->route('home')->with('error', 'Thanh toán không thành công');
        }

        DB::beginTransaction();
        try {
            $qrService = new QRCodeService();
            $emailService = new EmailService();

            $bookingCode = $qrService->generateBookingCode();

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $paymentData['showtime_id'],
                'booking_code' => $bookingCode,
                'total_amount' => $paymentData['total_amount'],
                'status' => 'paid',
            ]);

            $qrCodeUrl = $qrService->generateBookingQRCode($bookingCode);
            $booking->update(['qr_code_url' => $qrCodeUrl]);

            foreach ($paymentData['seat_details'] as $seatDetail) {
                Ticket::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seatDetail['id'],
                    'price' => $seatDetail['price']
                ]);
            }

            if (isset($paymentData['voucher_id']) && $paymentData['voucher_id']) {
                $voucher = \App\Models\Voucher::find($paymentData['voucher_id']);
                if ($voucher) {
                    $booking->vouchers()->attach($voucher->id);
                    $voucher->increment('usage_count');
                }
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $paymentData['total_amount'],
                'payment_method' => 'mock',
                'transaction_id' => $orderId,
                'status' => 'completed',
                'payment_time' => now(),
                'payment_data' => json_encode(['mode' => 'mock', 'order_id' => $orderId])
            ]);

            SeatReservation::where('user_id', Auth::id())
                ->where('showtime_id', $paymentData['showtime_id'])
                ->whereIn('seat_id', $paymentData['seat_ids'])
                ->delete();

            DB::commit();

            try {
                $this->awardLoyaltyPoints(Auth::id(), $booking);
            } catch (\Exception $e) {
                Log::error('Failed to award loyalty points: ' . $e->getMessage());
            }

            try {
                $emailService->sendBookingConfirmation($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send booking email: ' . $e->getMessage());
            }

            session()->forget(['payment_data', 'payment_id']);

            // Clean up Redis
            $sessionId = session()->getId();
            \Illuminate\Support\Facades\Redis::del("payment_session:{$sessionId}");

            return redirect()->route('booking.success', $booking->id)
                ->with('success', 'Đặt vé thành công! Vui lòng kiểm tra email để nhận mã QR.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mock Payment processing error', ['error' => $e->getMessage()]);

            return redirect()->route('home')->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
        }
    }

    private function awardLoyaltyPoints($userId, Booking $booking)
    {
        $loyaltyPoints = UserLoyaltyPoint::firstOrCreate(
            ['user_id' => $userId],
            ['total_points' => 0, 'current_tier_id' => LoyaltyTier::first()->id]
        );

        $pointsToAward = floor($booking->total_amount / 1000);

        $loyaltyPoints->addPoints(
            $pointsToAward,
            'earn',
            'Đặt vé thành công - Mã ' . $booking->booking_code,
            $booking->id
        );
    }

    public function validateVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $paymentData = session('payment_data');
        if (!$paymentData) {
            return response()->json(['valid' => false, 'message' => 'Phiên đặt vé đã hết hạn'], 400);
        }

        $voucher = \App\Models\Voucher::where('code', $request->voucher_code)->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá không tồn tại']);
        }

        $showtime = ShowTime::findOrFail($paymentData['showtime_id']);
        $movieId = $showtime->movie_id;
        $totalBeforeDiscount = array_sum(array_column($paymentData['seat_details'], 'price'));

        $validation = $voucher->isValid($movieId, $totalBeforeDiscount);

        if (!$validation['valid']) {
            return response()->json($validation);
        }

        $discountAmount = $voucher->calculateDiscount($totalBeforeDiscount);
        $loyaltyDiscount = $paymentData['loyalty_discount'] ?? 0;
        $finalTotal = max(0, $totalBeforeDiscount - $loyaltyDiscount - $discountAmount);

        return response()->json([
            'valid' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'voucher' => [
                'code' => $voucher->code,
                'description' => $voucher->description,
                'discount_amount' => $discountAmount,
            ],
            'pricing' => [
                'subtotal' => $totalBeforeDiscount,
                'loyalty_discount' => $loyaltyDiscount,
                'voucher_discount' => $discountAmount,
                'final_total' => $finalTotal,
            ]
        ]);
    }
}
