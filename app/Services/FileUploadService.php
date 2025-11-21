<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function uploadToS3(UploadedFile $file, string $folder, ?string $oldFileUrl = null): string
    {
        $this->validateFile($file, $folder);
        $filename = $this->generateUniqueFilename($file);
        $path = $file->storeAs($folder, $filename, 's3');

        if ($oldFileUrl) {
            $this->deleteFromS3($oldFileUrl);
        }

        return Storage::disk('s3')->url($path);
    }

    public function deleteFromS3(string $fileUrl): bool
    {
        try {
            $path = parse_url($fileUrl, PHP_URL_PATH);
            $path = ltrim($path, '/');

            $bucket = config('filesystems.disks.s3.bucket');
            if (Str::startsWith($path, $bucket . '/')) {
                $path = Str::after($path, $bucket . '/');
            }

            $result = Storage::disk('s3')->delete($path);

            \Log::info("Deleted file from S3", [
                'url' => $fileUrl,
                'path' => $path,
                'result' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            \Log::warning("Failed to delete file from S3: {$fileUrl}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function validateFile(UploadedFile $file, string $folder): void
    {
        $rules = [
            'avatars' => ['image', 'max:2048', 'mimes:jpeg,png,jpg,gif'],
            'photos' => ['image', 'max:2048', 'mimes:jpeg,png,jpg,gif'],
            'posters' => ['image', 'max:5120', 'mimes:jpeg,png,jpg,webp'],
            'trailers' => ['mimetypes:video/mp4,video/x-msvideo,video/quicktime', 'max:102400'],
            'qrcodes' => ['image', 'max:2048', 'mimes:png']
        ];

        if (!isset($rules[$folder])) {
            throw new \InvalidArgumentException("Invalid folder: {$folder}");
        }

        $validator = validator(['file' => $file], ['file' => $rules[$folder]]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);

        return "{$timestamp}_{$random}.{$extension}";
    }

    public function getS3Url(string $path, bool $temporary = false, int $expiresInMinutes = 60): ?string
    {
        if (Storage::disk('s3')->exists($path)) {
            if ($temporary) {
                return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($expiresInMinutes));
            }

            return Storage::disk('s3')->url($path);
        }

        return null;
    }

    public function uploadAvatar(UploadedFile $file, ?string $oldAvatarUrl = null): string
    {
        return $this->uploadToS3($file, 'avatars', $oldAvatarUrl);
    }

    public function uploadPhoto(UploadedFile $file, ?string $oldPhotoUrl = null): string
    {
        return $this->uploadToS3($file, 'photos', $oldPhotoUrl);
    }

    public function uploadPoster(UploadedFile $file, ?string $oldPosterUrl = null): string
    {
        return $this->uploadToS3($file, 'posters', $oldPosterUrl);
    }

    public function uploadTrailer(UploadedFile $file, ?string $oldTrailerUrl = null): string
    {
        return $this->uploadToS3($file, 'trailers', $oldTrailerUrl);
    }
}
