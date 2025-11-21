<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class OTPController extends Controller
{
    protected $emailService;
    protected $otpService;

    public function __construct(EmailService $emailService, OTPService $otpService)
    {
        $this->emailService = $emailService;
        $this->otpService = $otpService;
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type' => 'required|in:registration,password_reset',
            'name' => 'required_if:type,registration|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $type = $request->type;
        $name = $request->name ?? '';

        if ($type === 'password_reset') {
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email không tồn tại trong hệ thống!'
                ], 404);
            }
            $name = $user->name;
        }

        if ($type === 'registration') {
            if (User::where('email', $email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email đã được sử dụng!'
                ], 400);
            }
        }

        // Delete old OTP if exists
        $this->otpService->deleteOTP($email, $type);

        // Generate new OTP and store in Redis
        $otpData = $this->otpService->generateOTP($email, $type);

        $sent = false;
        if ($type === 'registration') {
            $sent = $this->emailService->sendRegistrationOTP($email, $name, $otpData['otp']);
        } else {
            $sent = $this->emailService->sendPasswordResetOTP($email, $name, $otpData['otp']);
        }

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại!'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mã OTP đã được gửi đến email của bạn!',
            'expires_in' => 600 // 10 minutes in seconds
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'type' => 'required|in:registration,password_reset'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify OTP from Redis
        if (!$this->otpService->verifyOTP($request->email, $request->otp, $request->type)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã OTP không đúng hoặc đã hết hạn!'
            ], 400);
        }

        // Mark OTP as used (delete from Redis)
        $this->otpService->markAsUsed($request->email, $request->type);

        return response()->json([
            'success' => true,
            'message' => 'Xác thực OTP thành công!',
            'verification_token' => base64_encode($request->email . '|' . time())
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'verification_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $decoded = base64_decode($request->verification_token);
        $parts = explode('|', $decoded);

        if (count($parts) !== 2 || $parts[0] !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Token xác thực không hợp lệ!'
            ], 400);
        }

        if ((time() - intval($parts[1])) > 1800) {
            return response()->json([
                'success' => false,
                'message' => 'Token đã hết hạn. Vui lòng thực hiện lại!'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại!'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đặt lại mật khẩu thành công!'
        ]);
    }
}
