<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    public function generateBookingQRCode($bookingCode)
    {
        $qrBinary = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($bookingCode);

        $tmpPath = sys_get_temp_dir() . '\\' . $bookingCode . '.png';
        file_put_contents($tmpPath, $qrBinary);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tmpPath,
            $bookingCode . '.png',
            'image/png',
            null,
            true
        );

        $fileUploadService = app(\App\Services\FileUploadService::class);

        return $fileUploadService->uploadToS3($uploadedFile, 'qrcodes');
    }

    public function verifyBookingCode($bookingCode)
    {
        return \App\Models\Booking::where('booking_code', $bookingCode)->exists();
    }

    public function generateBookingCode()
    {
        do {
            $code = 'BK-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
        } while (\App\Models\Booking::where('booking_code', $code)->exists());

        return $code;
    }

    public function deleteQRCode($qrCodeUrl)
    {
        if (!$qrCodeUrl) {
            return false;
        }

        $path = str_replace('/storage/', '', parse_url($qrCodeUrl, PHP_URL_PATH));
        return Storage::disk('public')->delete($path);
    }
}
