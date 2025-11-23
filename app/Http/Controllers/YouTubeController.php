<?php

namespace App\Http\Controllers;

use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class YouTubeController extends Controller
{
    private $youtubeService;

    public function __construct()
    {
        // Don't initialize YouTubeService in constructor to avoid errors if credentials are missing
        // Initialize it in the method instead
    }

    /**
     * Upload video to YouTube
     * POST /api/youtube/upload
     */
    public function uploadVideo(Request $request)
    {
        try {
            // Initialize YouTubeService here to catch initialization errors
            try {
                $youtubeService = app(YouTubeService::class);
            } catch (\Exception $e) {
                Log::error('Failed to initialize YouTubeService', [
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'YouTube service not configured: ' . $e->getMessage(),
                    'hint' => 'Please check your .env file has YOUTUBE_CLIENT_ID, YOUTUBE_CLIENT_SECRET, and YOUTUBE_REFRESH_TOKEN',
                ], 500);
            }

            Log::info('YouTube upload request received', [
                'has_video' => $request->hasFile('video'),
                'all_inputs' => array_keys($request->all()),
            ]);

            // Validate request
            $validator = Validator::make($request->all(), [
                'video' => 'required|file|mimes:mp4,mov,avi,mkv|max:102400', // Max 100MB
                'title' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:5000',
                'privacy' => 'nullable|in:private,public,unlisted',
            ]);

            if ($validator->fails()) {
                Log::warning('YouTube upload validation failed', [
                    'errors' => $validator->errors()->all(),
                ]);
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                ], 400);
            }

            // Get video file
            $videoFile = $request->file('video');
            $title = $request->input('title', 'Property Video');
            $description = $request->input('description', '');
            $privacy = $request->input('privacy', 'unlisted');

            // Store video temporarily
            $tempPath = $videoFile->storeAs('temp', 'video_' . time() . '.' . $videoFile->getClientOriginalExtension(), 'local');
            $fullPath = storage_path('app/' . $tempPath);

            Log::info('Video received for YouTube upload', [
                'title' => $title,
                'size' => $videoFile->getSize(),
                'path' => $fullPath,
            ]);

            // Upload to YouTube
            $result = $youtubeService->uploadVideo($fullPath, $title, $description, $privacy);

            // Delete temporary file
            Storage::delete($tempPath);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'video_url' => $result['video_url'],
                    'video_id' => $result['video_id'],
                    'message' => 'Video uploaded to YouTube successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('YouTube upload endpoint error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to upload video: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get OAuth URL for initial setup
     * GET /api/youtube/auth-url
     */
    public function getAuthUrl()
    {
        try {
            $youtubeService = app(YouTubeService::class);
            $authUrl = $youtubeService->getAuthUrl();
            return response()->json([
                'success' => true,
                'auth_url' => $authUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * OAuth callback (for initial setup)
     * GET /api/youtube/callback
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->query('code');
            if (!$code) {
                return response()->json([
                    'success' => false,
                    'error' => 'No authorization code provided',
                ], 400);
            }

            $youtubeService = app(YouTubeService::class);
            $result = $youtubeService->exchangeCodeForToken($code);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Authorization successful! Add this to your .env file:',
                    'refresh_token' => $result['refresh_token'],
                    'instructions' => 'Add YOUTUBE_REFRESH_TOKEN=' . $result['refresh_token'] . ' to your .env file',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

