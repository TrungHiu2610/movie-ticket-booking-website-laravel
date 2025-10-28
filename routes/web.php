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
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

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
});
