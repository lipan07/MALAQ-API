<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EngloVideoService
{
    /**
     * Storage folder under public disk.
     * Full path: storage_path('app/public/engloPoster')
     */
    public const STORAGE_DIR = 'engloPoster';

    /**
     * Store uploaded video as-is in engloPoster.
     * Requires: php artisan storage:link
     */
    public function storeAndProcess(UploadedFile $file): ?string
    {
        $originalName = $file->getClientOriginalName();

        Log::info('EngloVideoService: upload started', [
            'original_name' => $originalName,
            'size_bytes' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);

        $disk = Storage::disk('public');
        $dir = self::STORAGE_DIR;

        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
            Log::info('EngloVideoService: created storage directory', ['dir' => $dir]);
        }

        $extension = $file->getClientOriginalExtension() ?: 'mp4';
        if (! in_array(strtolower($extension), ['mp4', 'webm', 'mov'], true)) {
            $extension = 'mp4';
        }
        $filename = Str::uuid() . '.' . $extension;
        $relativePath = $dir . '/' . $filename;

        $stored = $file->storeAs($dir, $filename, 'public');
        Log::info('EngloVideoService: file stored', ['returned_path' => $stored, 'relative_path' => $relativePath]);

        if (! $disk->exists($relativePath)) {
            Log::warning('EngloVideoService: file was not written', ['relative_path' => $relativePath]);
            return null;
        }

        Log::info('EngloVideoService: upload completed', [
            'relative_path' => $relativePath,
            'size_bytes' => File::size($disk->path($relativePath)),
        ]);
        return $relativePath;
    }

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

    public function videoUrl(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }
        return Storage::disk('public')->url($relativePath);
    }
}
