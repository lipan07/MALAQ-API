<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BackblazeService
{
    /**
     * Generate signed download URL for private Backblaze files
     * 
     * @param string $fileUrl The original file URL from Backblaze
     * @return string|null Signed URL or null on failure
     */
    public function getSignedUrl(string $fileUrl): ?string
    {
        try {
            // Extract fileName from URL
            // URL format: https://fXXX.backblazeb2.com/file/bucketName/fileName
            $urlParts = parse_url($fileUrl);
            if (!isset($urlParts['path'])) {
                return $fileUrl; // Return original if can't parse
            }

            $pathParts = explode('/file/', $urlParts['path']);
            if (count($pathParts) < 2) {
                return $fileUrl; // Return original if invalid format
            }

            $bucketAndFile = $pathParts[1];
            $bucketAndFileParts = explode('/', $bucketAndFile, 2);
            if (count($bucketAndFileParts) < 2) {
                return $fileUrl; // Return original if invalid format
            }

            $bucketName = $bucketAndFileParts[0];
            $fileName = urldecode($bucketAndFileParts[1]); // Decode URL-encoded file name

            $accountId = env('BACKBLAZE_ACCOUNT_ID');
            $applicationKey = env('BACKBLAZE_APPLICATION_KEY');
            $bucketId = env('BACKBLAZE_BUCKET_ID');

            if (!$accountId || !$applicationKey || !$bucketId) {
                Log::warning('Backblaze credentials not configured for signed URL generation');
                return $fileUrl; // Return original URL if credentials not available
            }

            // Authorize with Backblaze
            $authResponse = $this->authorizeAccount($accountId, $applicationKey);
            if (!$authResponse['success']) {
                Log::error('Failed to authorize with Backblaze for signed URL');
                return $fileUrl; // Return original URL on auth failure
            }

            $authToken = $authResponse['authorizationToken'];
            $apiUrl = $authResponse['apiUrl'];
            $downloadUrl = $authResponse['downloadUrl'];

            // Get download authorization (valid for 24 hours)
            $ch = curl_init($apiUrl . '/b2api/v2/b2_get_download_authorization');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $authToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'bucketId' => $bucketId,
                'fileNamePrefix' => $fileName,
                'validDurationInSeconds' => 86400, // 24 hours
            ]));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                // Fallback: Use auth token directly (less secure but works)
                $signedUrl = $downloadUrl . '/file/' . $bucketName . '/' . urlencode($fileName) . '?Authorization=' . urlencode($authToken);
                return $signedUrl;
            }

            $data = json_decode($response, true);
            $downloadAuthToken = $data['authorizationToken'] ?? $authToken;

            // Construct signed URL
            $signedUrl = $downloadUrl . '/file/' . $bucketName . '/' . urlencode($fileName) . '?Authorization=' . urlencode($downloadAuthToken);

            return $signedUrl;
        } catch (\Exception $e) {
            Log::error('Error generating Backblaze signed URL: ' . $e->getMessage());
            return $fileUrl; // Return original URL on error
        }
    }

    /**
     * Authorize with Backblaze B2
     */
    private function authorizeAccount($accountId, $applicationKey)
    {
        $credentials = base64_encode($accountId . ':' . $applicationKey);

        $ch = curl_init('https://api.backblazeb2.com/b2api/v2/b2_authorize_account');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $credentials,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['success' => false, 'error' => $response];
        }

        $data = json_decode($response, true);
        return [
            'success' => true,
            'authorizationToken' => $data['authorizationToken'],
            'apiUrl' => $data['apiUrl'],
            'downloadUrl' => $data['downloadUrl'],
        ];
    }
}
