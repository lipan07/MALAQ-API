<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom ValidatePostSize that allows larger uploads for specific routes
 * This replaces Laravel's ValidatePostSize for routes that need large file uploads
 */
class CustomValidatePostSize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the maximum post size from PHP configuration
        $maxSize = $this->getPostMaxSize();
        
        // Check if Content-Length header is set and exceeds max size
        if ($request->server('CONTENT_LENGTH') > $maxSize) {
            return response()->json([
                'message' => 'The POST data is too large. Maximum allowed size: ' . $this->formatBytes($maxSize),
            ], 413);
        }

        return $next($request);
    }

    /**
     * Get the maximum post size in bytes
     */
    protected function getPostMaxSize(): int
    {
        $maxSize = ini_get('post_max_size');
        return $this->convertToBytes($maxSize);
    }

    /**
     * Convert PHP size string to bytes
     */
    protected function convertToBytes(string $size): int
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;

        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }

        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

