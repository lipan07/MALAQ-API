<?php

namespace App\Services;

use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YouTubeService
{
    private $client;
    private $youtube;

    public function __construct()
    {
        $this->initializeClient();
    }

    /**
     * Initialize Google Client with OAuth credentials
     */
    private function initializeClient()
    {
        $this->client = new Google_Client();
        
        // Set OAuth 2.0 credentials
        $this->client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $this->client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        
        // Set scopes
        $this->client->setScopes([
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube',
        ]);

        // Set refresh token (stored in .env)
        $refreshToken = env('YOUTUBE_REFRESH_TOKEN');
        if ($refreshToken) {
            try {
                $accessToken = $this->client->refreshToken($refreshToken);
                if ($accessToken) {
                    $this->client->setAccessToken($accessToken);
                }
            } catch (\Exception $e) {
                Log::error('Failed to refresh YouTube token', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->youtube = new Google_Service_YouTube($this->client);
    }

    /**
     * Upload video to YouTube
     * 
     * @param string $videoPath Local path to video file
     * @param string $title Video title
     * @param string $description Video description
     * @param string $privacy Privacy status: 'private', 'public', 'unlisted'
     * @return array
     */
    public function uploadVideo($videoPath, $title = 'Property Video', $description = '', $privacy = 'unlisted')
    {
        try {
            // Check if file exists
            if (!file_exists($videoPath)) {
                throw new \Exception("Video file not found: {$videoPath}");
            }

            // Create video snippet
            $snippet = new Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($title);
            $snippet->setDescription($description ?: 'Property video uploaded from Reuse App');
            $snippet->setTags(['property', 'real-estate', 'reuse-app']);
            $snippet->setCategoryId('22'); // People & Blogs

            // Create video status
            $status = new Google_Service_YouTube_VideoStatus();
            $status->setPrivacyStatus($privacy); // 'private', 'public', or 'unlisted'

            // Create video object
            $video = new Google_Service_YouTube_Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);

            // Upload video
            Log::info('Starting YouTube upload', [
                'title' => $title,
                'file' => $videoPath,
                'size' => filesize($videoPath),
            ]);

            $response = $this->youtube->videos->insert(
                'snippet,status',
                $video,
                [
                    'data' => file_get_contents($videoPath),
                    'mimeType' => 'video/*',
                    'uploadType' => 'multipart',
                ]
            );

            $videoId = $response->getId();
            $videoUrl = "https://www.youtube.com/watch?v={$videoId}";

            Log::info('YouTube upload successful', [
                'video_id' => $videoId,
                'video_url' => $videoUrl,
            ]);

            return [
                'success' => true,
                'video_id' => $videoId,
                'video_url' => $videoUrl,
                'data' => $response,
            ];

        } catch (\Exception $e) {
            Log::error('YouTube upload failed', [
                'error' => $e->getMessage(),
                'file' => $videoPath,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get refresh token URL (for initial setup)
     * This URL should be visited once to get the refresh token
     */
    public function getAuthUrl()
    {
        $this->client->setRedirectUri(env('YOUTUBE_REDIRECT_URI', env('APP_URL') . '/api/youtube/callback'));
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for refresh token
     */
    public function exchangeCodeForToken($code)
    {
        try {
            $this->client->setRedirectUri(env('YOUTUBE_REDIRECT_URI', env('APP_URL') . '/api/youtube/callback'));
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($accessToken['refresh_token'])) {
                return [
                    'success' => true,
                    'refresh_token' => $accessToken['refresh_token'],
                    'access_token' => $accessToken['access_token'],
                ];
            }

            return [
                'success' => false,
                'error' => 'No refresh token received',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

