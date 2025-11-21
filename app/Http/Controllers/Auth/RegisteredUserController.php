<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\EmailService;
use App\Services\OTPService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    protected $emailService;
    protected $otpService;

    public function __construct(EmailService $emailService, OTPService $otpService)
    {
        $this->emailService = $emailService;
        $this->otpService = $otpService;
    }

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Delete old OTP if exists
        $this->otpService->deleteOTP($request->email, 'registration');

        // Generate new OTP and store in Redis
        $otpData = $this->otpService->generateOTP($request->email, 'registration');

        $sent = $this->emailService->sendRegistrationOTP($request->email, $request->name, $otpData['otp']);

        if (!$sent) {
            return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng thử lại!'])->withInput();
        }

        session([
            'registration_data' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ],
            'otp_expires_at' => $otpData['expires_at']->timestamp
        ]);

        return redirect()->route('register.verify');
    }

    public function showVerify()
    {
        if (!session('registration_data')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }

    public function verifyOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $registrationData = session('registration_data');
        if (!$registrationData) {
            return redirect()->route('register');
        }

        // Verify OTP from Redis
        if (!$this->otpService->verifyOTP($registrationData['email'], $request->otp, 'registration')) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng hoặc đã hết hạn!']);
        }

        // Mark OTP as used (delete from Redis)
        $this->otpService->markAsUsed($registrationData['email'], 'registration');

        $customerRole = Role::where('name', 'customer')->firstOrFail();

        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'password' => Hash::make($registrationData['password']),
            'role_id' => $customerRole->id,
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        session()->forget(['registration_data', 'otp_expires_at']);

        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }

    public function resendOTP(Request $request)
    {
        $registrationData = session('registration_data');
        if (!$registrationData) {
            return response()->json([
                'success' => false,
                'message' => 'Session hết hạn. Vui lòng đăng ký lại!'
            ], 400);
        }

        // Check if can resend
        $canResend = $this->otpService->canResend($registrationData['email'], 'registration');
        if (!$canResend['can_resend']) {
            return response()->json([
                'success' => false,
                'message' => $canResend['message']
            ], 400);
        }

        // Increment resend count
        $resendCount = $this->otpService->incrementResendCount($registrationData['email'], 'registration');

        // Generate new OTP
        $otpData = $this->otpService->generateOTP($registrationData['email'], 'registration');

        $sent = $this->emailService->sendRegistrationOTP(
            $registrationData['email'],
            $registrationData['name'],
            $otpData['otp']
        );

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại!'
            ], 500);
        }

        session(['otp_expires_at' => $otpData['expires_at']->timestamp]);

        return response()->json([
            'success' => true,
            'message' => 'Mã OTP mới đã được gửi!',
            'expires_in' => 600,
            'resend_count' => $resendCount,
            'remaining_resends' => 2 - $resendCount
        ]);
    }
}
