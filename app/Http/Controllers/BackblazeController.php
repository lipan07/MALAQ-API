<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackblazeController extends Controller
{
    /**
     * Get Backblaze credentials for direct upload
     * GET /api/backblaze/credentials
     * 
     * This endpoint provides Backblaze credentials to authenticated users
     * so they can upload files directly from the React Native app.
     */
    public function getCredentials(Request $request)
    {
        try {
            $accountId = env('BACKBLAZE_ACCOUNT_ID');
            $applicationKey = env('BACKBLAZE_APPLICATION_KEY');
            $bucketId = env('BACKBLAZE_BUCKET_ID');
            $bucketName = env('BACKBLAZE_BUCKET_NAME');

            if (!$accountId || !$applicationKey || !$bucketId || !$bucketName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backblaze credentials not configured on server',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'account_id' => $accountId,
                'application_key' => $applicationKey,
                'bucket_id' => $bucketId,
                'bucket_name' => $bucketName,
            ]);
        } catch (\Exception $e) {
            Log::error('Backblaze credentials error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get Backblaze credentials: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload large video through backend to Backblaze
     * POST /api/backblaze/upload-video
     * 
     * For large files (>50MB), upload through backend to avoid memory issues
     */
    public function uploadVideo(Request $request)
    {
        try {
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,mkv|max:1024000', // Max 1GB
                'fileName' => 'nullable|string',
            ]);

            $videoFile = $request->file('video');
            $fileName = $request->input('fileName', 'videos/' . time() . '_' . $videoFile->getClientOriginalName());

            if (!str_starts_with($fileName, 'videos/')) {
                $fileName = 'videos/' . $fileName;
            }

            $fileSize = $videoFile->getSize();

            // Get Backblaze credentials
            $accountId = env('BACKBLAZE_ACCOUNT_ID');
            $applicationKey = env('BACKBLAZE_APPLICATION_KEY');
            $bucketId = env('BACKBLAZE_BUCKET_ID');
            $bucketName = env('BACKBLAZE_BUCKET_NAME');

            if (!$accountId || !$applicationKey || !$bucketId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backblaze credentials not configured',
                ], 500);
            }

            // Authorize with Backblaze
            $authResponse = $this->authorizeAccount($accountId, $applicationKey);
            if (!$authResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authorize with Backblaze',
                ], 500);
            }

            $authToken = $authResponse['authorizationToken'];
            $apiUrl = $authResponse['apiUrl'];
            $downloadUrl = $authResponse['downloadUrl'];

            // Get upload URL
            $uploadUrlResponse = $this->getUploadUrl($apiUrl, $authToken, $bucketId);
            if (!$uploadUrlResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get upload URL',
                ], 500);
            }

            $uploadUrl = $uploadUrlResponse['uploadUrl'];
            $uploadAuthToken = $uploadUrlResponse['authorizationToken'];

            // Calculate SHA1
            $sha1Hash = hash_file('sha1', $videoFile->getRealPath());

            // Upload to Backblaze
            $ch = curl_init($uploadUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $uploadAuthToken,
                'X-Bz-File-Name: ' . urlencode($fileName),
                'Content-Type: video/mp4',
                'Content-Length: ' . $fileSize,
                'X-Bz-Content-Sha1: ' . $sha1Hash,
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($videoFile->getRealPath()));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backblaze upload failed: ' . $response,
                ], 500);
            }

            $result = json_decode($response, true);
            $fileUrl = $downloadUrl . '/file/' . $bucketName . '/' . $fileName;

            return response()->json([
                'success' => true,
                'fileUrl' => $fileUrl,
                'fileId' => $result['fileId'],
                'fileName' => $result['fileName'] ?? $fileName,
                'size' => $result['contentLength'] ?? $fileSize,
                'uploadTimestamp' => $result['uploadTimestamp'] ?? time(),
            ]);
        } catch (\Exception $e) {
            Log::error('Backblaze video upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

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

    private function getUploadUrl($apiUrl, $authToken, $bucketId)
    {
        $ch = curl_init($apiUrl . '/b2api/v2/b2_get_upload_url');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $authToken,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['bucketId' => $bucketId]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['success' => false, 'error' => $response];
        }

        $data = json_decode($response, true);
        return [
            'success' => true,
            'uploadUrl' => $data['uploadUrl'],
            'authorizationToken' => $data['authorizationToken'],
        ];
    }
}
