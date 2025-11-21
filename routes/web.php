<?php

use App\Http\Controllers\Admin\ActorController;
use App\Http\Controllers\Admin\CinemaController;
use App\Http\Controllers\Admin\DirectorController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\SeatTypeController;
use App\Http\Controllers\Admin\ShowtimeController;
use App\Http\Controllers\Admin\SurchargeController;
use App\Http\Controllers\Admin\TheaterController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\CinemaController as UserCinemaController;
use App\Http\Controllers\User\MovieController as UserMovieController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\RatingController;
use App\Http\Controllers\Staff\TicketScannerController;
use App\Http\Controllers\Staff\RefundController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\AIChatbotController;
use App\Http\Controllers\Admin\EmbeddingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// ================= USER ROUTES =================
Route::get('/', [HomeController::class, 'index'])->name('home');

// AI Chatbot Routes
Route::get('/chatbot', [AIChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/chat', [AIChatbotController::class, 'chat'])->name('chatbot.chat');
Route::get('/chatbot/history', [AIChatbotController::class, 'history'])->name('chatbot.history');
Route::post('/chatbot/clear', [AIChatbotController::class, 'clear'])->name('chatbot.clear');

// OTP Routes (for registration and password reset)
Route::prefix('api/otp')->name('otp.')->group(function () {
    Route::post('/send', [OTPController::class, 'sendOTP'])->name('send');
    Route::post('/verify', [OTPController::class, 'verifyOTP'])->name('verify');
    Route::post('/reset-password', [OTPController::class, 'resetPassword'])->name('reset-password');
});

// Movies
Route::prefix('movies')->name('movies.')->group(function () {
    Route::get('/', [UserMovieController::class, 'index'])->name('index');
    Route::get('/{movie}', [UserMovieController::class, 'show'])->name('show');
});

// Cinemas
Route::prefix('cinemas')->name('cinemas.')->group(function () {
    Route::get('/', [UserCinemaController::class, 'index'])->name('index');
    Route::get('/{cinema}', [UserCinemaController::class, 'show'])->name('show');
});

// Booking routes (require authentication)
Route::middleware('auth')->group(function () {
    // User profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Booking flow
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/seats/{showtime}', [BookingController::class, 'selectSeats'])->name('seats');
        Route::post('/reserve', [BookingController::class, 'reserveSeats'])->name('reserve');
        Route::get('/seat-status/{showtime}', [BookingController::class, 'getSeatStatus'])->name('seat-status');
    });

    // Payment routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('/show', [PaymentController::class, 'showPaymentPage'])->name('show');
        Route::post('/create', [PaymentController::class, 'createPayment'])->name('create');
        Route::get('/callback', [PaymentController::class, 'callback'])->name('callback');
        Route::post('/ipn', [PaymentController::class, 'ipn'])->name('ipn');
        Route::post('/validate-voucher', [PaymentController::class, 'validateVoucher'])->name('validate-voucher');
    });

    Route::get('/booking/success/{booking}', [PaymentController::class, 'success'])->name('booking.success');

    // Booking history
    Route::get('/my-bookings', [BookingController::class, 'history'])->name('bookings.history');

    // Refund request (user side)
    Route::post('/bookings/{booking}/refund-request', [BookingController::class, 'requestRefund'])->name('bookings.refund-request');

    // Rating routes
    Route::prefix('ratings')->name('ratings.')->group(function () {
        Route::get('/{booking}/create', [RatingController::class, 'create'])->name('create');
        Route::post('/{booking}', [RatingController::class, 'store'])->name('store');
        Route::put('/{rating}', [RatingController::class, 'update'])->name('update');
        Route::delete('/{rating}', [RatingController::class, 'destroy'])->name('destroy');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';

// ================= STAFF ROUTES =================
Route::prefix('staff')->middleware(['auth', 'staff'])->name('staff.')->group(function () {
    // Ticket scanner
    Route::prefix('scanner')->name('scanner.')->group(function () {
        Route::get('/', [TicketScannerController::class, 'index'])->name('index');
        Route::post('/scan', [TicketScannerController::class, 'scan'])->name('scan');
        Route::post('/check-in', [TicketScannerController::class, 'checkIn'])->name('check-in');
        Route::post('/check-in/{booking_id}', [TicketScannerController::class, 'checkIn'])->name('check-in.booking');
        Route::get('/history', [TicketScannerController::class, 'history'])->name('history');
    });

    // Refund management
    Route::prefix('refund')->name('refund.')->group(function () {
        Route::get('/', [RefundController::class, 'index'])->name('index');
        Route::post('/search', [RefundController::class, 'search'])->name('search');
        Route::post('/scan', [RefundController::class, 'scanQR'])->name('scan');
        Route::get('/show/{booking}', [RefundController::class, 'show'])->name('show');
        Route::post('/process/{booking}', [RefundController::class, 'process'])->name('process');
        Route::get('/print/{refund}', [RefundController::class, 'printReceipt'])->name('print');
        Route::get('/download/{refund}', [RefundController::class, 'downloadPdf'])->name('download');
    });
});

// ================= ADMIN ROUTES =================
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Route cho trang dashboard admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('movies', MovieController::class);
    Route::resource('genres', GenreController::class);
    Route::resource('actors', ActorController::class);
    Route::resource('directors', DirectorController::class);
    Route::resource('cinemas', CinemaController::class);
    Route::resource('theaters', TheaterController::class);
    Route::resource('seat-types', SeatTypeController::class);
    Route::resource('showtimes', ShowtimeController::class);
    Route::get('/showtimes/check-conflicts', [ShowtimeController::class, 'checkConflicts'])->name('showtimes.check-conflicts');
    Route::resource('surcharges', SurchargeController::class);
    Route::resource('vouchers', VoucherController::class);

    // AI Embeddings Management
    Route::prefix('embeddings')->name('embeddings.')->group(function () {
        Route::get('/', [EmbeddingController::class, 'index'])->name('index');
        Route::post('/embed-all', [EmbeddingController::class, 'embedAll'])->name('embed-all');
        Route::post('/embed/{movieId}', [EmbeddingController::class, 'embedMovie'])->name('embed-movie');
    });
});
