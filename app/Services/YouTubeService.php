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
        // Don't initialize in constructor - initialize on first use
        // This allows the service to be instantiated even if credentials are missing
    }

    /**
     * Ensure client is initialized
     */
    private function ensureInitialized()
    {
        if (!$this->client) {
            $this->initializeClient();
        }
    }

    /**
     * Initialize Google Client with OAuth credentials
     */
    private function initializeClient()
    {
        try {
            $this->client = new Google_Client();

            // Set OAuth 2.0 credentials
            $clientId = env('YOUTUBE_CLIENT_ID');
            $clientSecret = env('YOUTUBE_CLIENT_SECRET');

            if (!$clientId || !$clientSecret) {
                throw new \Exception('YOUTUBE_CLIENT_ID and YOUTUBE_CLIENT_SECRET must be set in .env file');
            }

            $this->client->setClientId($clientId);
            $this->client->setClientSecret($clientSecret);
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
                    if ($accessToken && !isset($accessToken['error'])) {
                        $this->client->setAccessToken($accessToken);
                    } else {
                        Log::warning('Failed to refresh YouTube token', [
                            'error' => $accessToken['error'] ?? 'Unknown error',
                        ]);
                        throw new \Exception('Invalid or expired refresh token. Please get a new one.');
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to refresh YouTube token', [
                        'error' => $e->getMessage(),
                    ]);
                    throw new \Exception('Failed to authenticate with YouTube: ' . $e->getMessage());
                }
            } else {
                throw new \Exception('YOUTUBE_REFRESH_TOKEN must be set in .env file. See GET_REFRESH_TOKEN.md for instructions.');
            }

            $this->youtube = new Google_Service_YouTube($this->client);
        } catch (\Exception $e) {
            Log::error('Failed to initialize YouTube client', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
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
        $this->ensureInitialized();

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
        $this->ensureInitialized();
        $this->client->setRedirectUri(env('YOUTUBE_REDIRECT_URI', env('APP_URL') . '/api/youtube/callback'));
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for refresh token
     */
    public function exchangeCodeForToken($code)
    {
        $this->ensureInitialized();

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

