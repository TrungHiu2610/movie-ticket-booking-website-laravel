<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class OTPService
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const MAX_RESEND_COUNT = 2;

    /**
     * Generate and store OTP in Redis
     */
    public function generateOTP(string $email, string $type = 'registration'): array
    {
        $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $key = $this->getRedisKey($email, $type);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        // Get current resend count
        $currentData = $this->getOTP($email, $type);
        $resendCount = $currentData ? ($currentData['resend_count'] ?? 0) : 0;

        $data = [
            'otp' => $otp,
            'email' => $email,
            'type' => $type,
            'resend_count' => $resendCount,
            'expires_at' => $expiresAt->timestamp,
            'created_at' => now()->timestamp,
        ];

        // Store in Redis with expiry
        Redis::setex($key, self::OTP_EXPIRY_MINUTES * 60, json_encode($data));

        return [
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'resend_count' => $resendCount,
        ];
    }

    /**
     * Verify OTP from Redis
     */
    public function verifyOTP(string $email, string $otp, string $type = 'registration'): bool
    {
        $key = $this->getRedisKey($email, $type);
        $data = Redis::get($key);

        if (!$data) {
            return false;
        }

        $otpData = json_decode($data, true);

        // Check if OTP matches and not expired
        if ($otpData['otp'] === $otp && $otpData['expires_at'] > now()->timestamp) {
            return true;
        }

        return false;
    }

    /**
     * Mark OTP as used (delete from Redis)
     */
    public function markAsUsed(string $email, string $type = 'registration'): void
    {
        $key = $this->getRedisKey($email, $type);
        Redis::del($key);
    }

    /**
     * Get OTP data from Redis
     */
    public function getOTP(string $email, string $type = 'registration'): ?array
    {
        $key = $this->getRedisKey($email, $type);
        $data = Redis::get($key);

        if (!$data) {
            return null;
        }

        return json_decode($data, true);
    }

    /**
     * Check if can resend OTP
     */
    public function canResend(string $email, string $type = 'registration'): array
    {
        $data = $this->getOTP($email, $type);

        if (!$data) {
            return ['can_resend' => true, 'resend_count' => 0];
        }

        $resendCount = $data['resend_count'] ?? 0;

        if ($resendCount >= self::MAX_RESEND_COUNT) {
            return [
                'can_resend' => false,
                'resend_count' => $resendCount,
                'message' => 'Bạn đã gửi lại tối đa ' . self::MAX_RESEND_COUNT . ' lần. Vui lòng đăng ký lại!'
            ];
        }

        return ['can_resend' => true, 'resend_count' => $resendCount];
    }

    /**
     * Increment resend count
     */
    public function incrementResendCount(string $email, string $type = 'registration'): int
    {
        $data = $this->getOTP($email, $type);

        if (!$data) {
            return 0;
        }

        $data['resend_count'] = ($data['resend_count'] ?? 0) + 1;
        $key = $this->getRedisKey($email, $type);

        // Update with remaining TTL
        $ttl = Redis::ttl($key);
        if ($ttl > 0) {
            Redis::setex($key, $ttl, json_encode($data));
        }

        return $data['resend_count'];
    }

    /**
     * Delete OTP from Redis
     */
    public function deleteOTP(string $email, string $type = 'registration'): void
    {
        $key = $this->getRedisKey($email, $type);
        Redis::del($key);
    }

    /**
     * Generate Redis key
     */
    private function getRedisKey(string $email, string $type): string
    {
        return "otp:{$type}:{$email}";
    }
}
