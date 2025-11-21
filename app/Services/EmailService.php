<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;

class EmailService
{

    public function sendBookingConfirmation(Booking $booking)
    {
        try {
            $booking->load(['user', 'showtime.theater.cinema', 'showtime.movie', 'tickets.seat']);

            $data = [
                'booking' => $booking,
                'user' => $booking->user,
                'movie' => $booking->showtime->movie,
                'showtime' => $booking->showtime,
                'cinema' => $booking->showtime->theater->cinema,
                'theater' => $booking->showtime->theater,
                'tickets' => $booking->tickets,
            ];

            // Render the email view
            $html = view('emails.booking-confirmation', $data)->render();

            // Use Resend facade to send email
            Resend::emails()->send([
                'from' => config('mail.from.address'),
                'to' => [$booking->user->email],
                'subject' => 'Xác nhận đặt vé - ' . $booking->booking_code,
                'html' => $html,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }


    public function sendBookingCancellation(Booking $booking)
    {
        try {
            $booking->load(['user', 'showtime.movie']);

            $data = [
                'booking' => $booking,
                'user' => $booking->user,
                'movie' => $booking->showtime->movie,
            ];

            // Render the email view
            $html = view('emails.booking-cancellation', $data)->render();

            // Use Resend facade to send email
            Resend::emails()->send([
                'from' => config('mail.from.address'),
                'to' => [$booking->user->email],
                'subject' => 'Thông báo hủy vé - ' . $booking->booking_code,
                'html' => $html,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation email: ' . $e->getMessage());
            return false;
        }
    }


    public function sendShowtimeReminder(Booking $booking)
    {
        try {
            $booking->load(['user', 'showtime.theater.cinema', 'showtime.movie']);

            $data = [
                'booking' => $booking,
                'user' => $booking->user,
                'movie' => $booking->showtime->movie,
                'showtime' => $booking->showtime,
            ];

            // Render the email view
            $html = view('emails.showtime-reminder', $data)->render();

            // Use Resend facade to send email
            Resend::emails()->send([
                'from' => config('mail.from.address'),
                'to' => [$booking->user->email],
                'subject' => 'Nhắc nhở: Bạn có suất chiếu sắp diễn ra!',
                'html' => $html,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send showtime reminder email: ' . $e->getMessage());
            return false;
        }
    }


    public function sendRegistrationOTP($email, $name, $otp)
    {
        try {
            $data = [
                'name' => $name,
                'otp' => $otp,
                'expires_minutes' => 10
            ];

            // Render the email view
            $html = view('emails.registration-otp', $data)->render();

            // Use Resend facade to send email
            Resend::emails()->send([
                'from' => config('mail.from.address'),
                'to' => [$email],
                'subject' => 'Mã OTP xác thực đăng ký - UniCine',
                'html' => $html,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send registration OTP email: ' . $e->getMessage());
            return false;
        }
    }


    public function sendPasswordResetOTP($email, $name, $otp)
    {
        try {
            $data = [
                'name' => $name,
                'otp' => $otp,
                'expires_minutes' => 10
            ];

            // Render the email view
            $html = view('emails.password-reset-otp', $data)->render();

            // Use Resend facade to send email
            Resend::emails()->send([
                'from' => config('mail.from.address'),
                'to' => [$email],
                'subject' => 'Mã OTP đặt lại mật khẩu - UniCine',
                'html' => $html,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP email: ' . $e->getMessage());
            return false;
        }
    }
}
