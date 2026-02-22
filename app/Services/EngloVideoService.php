<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EngloVideoService
{
    /** Max duration in seconds (3 minutes). */
    public const MAX_DURATION_SECONDS = 180;

    /** Target width for mobile (360px). */
    public const TARGET_WIDTH = 360;

    /**
     * Storage folder under public disk.
     * Full path: storage_path('app/public/engloPoster') e.g. /var/www/html/MALAQ-API/storage/app/public/engloPoster
     */
    public const STORAGE_DIR = 'engloPoster';

    /**
     * Validate video duration (max 3 min) using ffprobe if available.
     */
    public function validateDuration(string $path): bool
    {
        if (! $this->ffprobeAvailable()) {
            return true; // skip check if ffprobe not installed
        }
        $duration = $this->getDurationSeconds($path);
        return $duration !== null && $duration <= self::MAX_DURATION_SECONDS;
    }

    /**
     * Get video duration in seconds via ffprobe.
     */
    public function getDurationSeconds(string $path): ?float
    {
        if (! $this->ffprobeAvailable()) {
            return null;
        }
        $path = escapeshellarg($path);
        $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$path} 2>/dev/null";
        $output = @shell_exec($cmd);
        if ($output === null || $output === '') {
            return null;
        }
        $seconds = (float) trim($output);
        return $seconds > 0 ? $seconds : null;
    }

    public function ffprobeAvailable(): bool
    {
        $which = PHP_OS_FAMILY === 'Windows' ? 'where ffprobe' : 'which ffprobe';
        $result = @shell_exec($which);

        return $result !== null && trim($result) !== '';
    }

    public function ffmpegAvailable(): bool
    {
        $which = PHP_OS_FAMILY === 'Windows' ? 'where ffmpeg' : 'which ffmpeg';
        $result = @shell_exec($which);

        return $result !== null && trim($result) !== '';
    }

    /**
     * Store and process video: resize to 360px width, compress, save to engloPoster.
     * Returns relative path (e.g. engloPoster/xxx.mp4) for DB, or null on failure.
     */
    public function storeAndProcess(UploadedFile $file): ?string
    {
        $fullPath = $file->getRealPath();
        if (! $this->validateDuration($fullPath)) {
            return null;
        }

        $disk = Storage::disk('public');
        $dir = self::STORAGE_DIR;
        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $extension = $file->getClientOriginalExtension() ?: 'mp4';
        if (! in_array(strtolower($extension), ['mp4', 'webm', 'mov'], true)) {
            $extension = 'mp4';
        }
        $filename = Str::uuid() . '.' . $extension;
        $relativePath = $dir . '/' . $filename;

        if ($this->ffmpegAvailable()) {
            $outputPath = $disk->path($relativePath);
            $success = $this->transcodeTo360($fullPath, $outputPath);
            if (! $success) {
                // Fallback: store original
                $file->storeAs($dir, $filename, 'public');
            }
        } else {
            $file->storeAs($dir, $filename, 'public');
        }

        return $relativePath;
    }

    /**
     * Transcode video to 360p and lower bitrate for small file size.
     */
    protected function transcodeTo360(string $inputPath, string $outputPath): bool
    {
        $inputPath = escapeshellarg($inputPath);
        $outputPath = escapeshellarg($outputPath);
        $w = self::TARGET_WIDTH;
        // -vf scale=360:-2 (height divisible by 2), -crf 28 (smaller file), -preset fast, -movflags +faststart for web
        $cmd = "ffmpeg -y -i {$inputPath} -vf scale={$w}:-2 -c:v libx264 -crf 28 -preset fast -movflags +faststart -c:a aac -b:a 96k {$outputPath} 2>/dev/null";
        $output = @shell_exec($cmd);

        return File::isFile($outputPath);
    }

    /**
     * Delete video file by relative path (e.g. engloPoster/xxx.mp4).
     */
    public function deleteVideo(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }
        try {
            Storage::disk('public')->delete($relativePath);
        } catch (\Throwable $e) {
            Log::warning('EngloVideoService: failed to delete video.', ['path' => $relativePath, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Full URL for the stored video (for API and frontend).
     */
    public function videoUrl(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }
        return Storage::disk('public')->url($relativePath);
    }
}
