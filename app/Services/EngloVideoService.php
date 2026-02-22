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
            Log::info('EngloVideoService: duration check skipped (ffprobe not available)');
            return true; // skip check if ffprobe not installed
        }
        $duration = $this->getDurationSeconds($path);
        $valid = $duration !== null && $duration <= self::MAX_DURATION_SECONDS;
        if ($duration !== null) {
            Log::info('EngloVideoService: duration check', [
                'path' => $path,
                'duration_seconds' => $duration,
                'max_allowed' => self::MAX_DURATION_SECONDS,
                'valid' => $valid,
            ]);
        } else {
            Log::warning('EngloVideoService: could not read duration (ffprobe returned empty)', ['path' => $path]);
        }
        return $valid;
    }

    /**
     * Get video duration in seconds via ffprobe.
     */
    public function getDurationSeconds(string $path): ?float
    {
        if (! $this->ffprobeAvailable()) {
            return null;
        }
        $pathEscaped = escapeshellarg($path);
        $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$pathEscaped} 2>&1";
        $output = @shell_exec($cmd);
        if ($output === null || trim($output) === '') {
            Log::warning('EngloVideoService: ffprobe returned no output', ['path' => $path, 'raw_output' => $output]);
            return null;
        }
        $seconds = (float) trim($output);
        if ($seconds <= 0) {
            Log::warning('EngloVideoService: ffprobe returned invalid duration', ['path' => $path, 'raw_output' => trim($output)]);
            return null;
        }
        return $seconds;
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
     * Requires: php artisan storage:link (so public/storage -> storage/app/public).
     */
    public function storeAndProcess(UploadedFile $file): ?string
    {
        $fullPath = $file->getRealPath();
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();
        $mime = $file->getMimeType();

        Log::info('EngloVideoService: upload started', [
            'original_name' => $originalName,
            'temp_path' => $fullPath,
            'size_bytes' => $size,
            'mime' => $mime,
        ]);

        if (! $this->validateDuration($fullPath)) {
            Log::warning('EngloVideoService: upload rejected (duration > 3 min)', [
                'original_name' => $originalName,
            ]);
            return null;
        }

        $disk = Storage::disk('public');
        $dir = self::STORAGE_DIR;
        $storageRoot = $disk->path('');

        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
            Log::info('EngloVideoService: created storage directory', ['dir' => $dir, 'absolute' => $disk->path($dir)]);
        }

        $extension = $file->getClientOriginalExtension() ?: 'mp4';
        if (! in_array(strtolower($extension), ['mp4', 'webm', 'mov'], true)) {
            $extension = 'mp4';
        }
        $filename = Str::uuid() . '.' . $extension;
        $relativePath = $dir . '/' . $filename;
        $absolutePath = $disk->path($relativePath);

        $ffmpegAvailable = $this->ffmpegAvailable();
        Log::info('EngloVideoService: storage plan', [
            'relative_path' => $relativePath,
            'absolute_path' => $absolutePath,
            'storage_root' => $storageRoot,
            'ffmpeg_available' => $ffmpegAvailable,
        ]);

        if ($ffmpegAvailable) {
            $success = $this->transcodeTo360($fullPath, $absolutePath);
            if (! $success) {
                Log::warning('EngloVideoService: transcode failed, falling back to storeAs', ['output_path' => $absolutePath]);
                $stored = $file->storeAs($dir, $filename, 'public');
                Log::info('EngloVideoService: storeAs result', ['returned_path' => $stored, 'expected' => $relativePath]);
            } else {
                Log::info('EngloVideoService: transcode succeeded', ['output_path' => $absolutePath]);
            }
        } else {
            Log::info('EngloVideoService: ffmpeg not available, storing original file');
            $stored = $file->storeAs($dir, $filename, 'public');
            Log::info('EngloVideoService: storeAs result', ['returned_path' => $stored, 'expected' => $relativePath]);
        }

        if (! $disk->exists($relativePath)) {
            Log::warning('EngloVideoService: file was not written', [
                'relative_path' => $relativePath,
                'absolute_path' => $absolutePath,
                'storage_root_exists' => is_dir($storageRoot),
                'dir_exists' => $disk->exists($dir),
            ]);
            return null;
        }

        $writtenSize = File::size($absolutePath);
        Log::info('EngloVideoService: upload completed', [
            'relative_path' => $relativePath,
            'written_size_bytes' => $writtenSize,
        ]);

        return $relativePath;
    }

    /**
     * Transcode video to 360p and lower bitrate for small file size.
     */
    protected function transcodeTo360(string $inputPath, string $outputPath): bool
    {
        $inputEscaped = escapeshellarg($inputPath);
        $outputEscaped = escapeshellarg($outputPath);
        $w = self::TARGET_WIDTH;
        $cmd = "ffmpeg -y -i {$inputEscaped} -vf scale={$w}:-2 -c:v libx264 -crf 28 -preset fast -movflags +faststart -c:a aac -b:a 96k {$outputEscaped} 2>&1";
        Log::info('EngloVideoService: running ffmpeg', ['input' => $inputPath, 'output' => $outputPath]);

        $output = @shell_exec($cmd);

        $exists = File::isFile($outputPath);
        if (! $exists) {
            Log::warning('EngloVideoService: ffmpeg did not produce output file', [
                'output_path' => $outputPath,
                'ffmpeg_output' => $output ? trim($output) : '(empty)',
            ]);
        }
        return $exists;
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
