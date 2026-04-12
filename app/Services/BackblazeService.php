<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BackblazeService
{
    /**
     * Public bucket: return the file URL unchanged, minus a stale ?Authorization=… query if present.
     * (Kept method name for callers; no signed download tokens.)
     */
    public function getSignedUrl(?string $fileUrl): ?string
    {
        if ($fileUrl === null || $fileUrl === '') {
            return null;
        }

        return $this->stripAuthorizationQuery($fileUrl);
    }

    /**
     * Build canonical B2 native URL when you only have the object key (fileName in bucket).
     * Set BACKBLAZE_PUBLIC_DOWNLOAD_BASE in .env, e.g. https://f005.backblazeb2.com
     */
    public function buildPublicFileUrl(string $fileName): ?string
    {
        $base = rtrim((string) env('BACKBLAZE_PUBLIC_DOWNLOAD_BASE', ''), '/');
        $bucket = (string) env('BACKBLAZE_BUCKET_NAME', '');
        if ($base === '' || $bucket === '') {
            return null;
        }

        return $base . '/file/' . $bucket . '/' . rawurlencode($fileName);
    }

    private function stripAuthorizationQuery(string $url): string
    {
        if (!str_contains($url, '?')) {
            return $url;
        }

        $parsed = parse_url($url);
        if (!is_array($parsed) || empty($parsed['query'])) {
            return $url;
        }

        parse_str($parsed['query'], $query);
        unset($query['Authorization']);

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        if ($host === '') {
            return $url;
        }

        $rebuilt = $scheme . '://' . $host;
        if (!empty($parsed['port'])) {
            $rebuilt .= ':' . $parsed['port'];
        }
        $rebuilt .= $parsed['path'] ?? '';

        if (!empty($query)) {
            $rebuilt .= '?' . http_build_query($query);
        }
        if (!empty($parsed['fragment'])) {
            $rebuilt .= '#' . $parsed['fragment'];
        }

        return $rebuilt;
    }

    /**
     * Authorize with Backblaze B2 (uploads / deletes)
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

    /**
     * Delete video from Backblaze B2
     *
     * @param string $fileId Backblaze file ID
     * @param string|null $fileName File name (optional)
     * @return array
     */
    public function deleteVideo(string $fileId, ?string $fileName = null): array
    {
        try {
            $accountId = env('BACKBLAZE_ACCOUNT_ID');
            $applicationKey = env('BACKBLAZE_APPLICATION_KEY');

            if (!$accountId || !$applicationKey) {
                return ['success' => false, 'message' => 'Backblaze credentials not configured'];
            }

            // Authorize with Backblaze
            $authResponse = $this->authorizeAccount($accountId, $applicationKey);
            if (!$authResponse['success']) {
                return ['success' => false, 'message' => 'Failed to authorize with Backblaze'];
            }

            $authToken = $authResponse['authorizationToken'];
            $apiUrl = $authResponse['apiUrl'];

            // Delete file
            $ch = curl_init($apiUrl . '/b2api/v2/b2_delete_file_version');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $authToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'fileId' => $fileId,
                'fileName' => $fileName ?? 'videos/unknown',
            ]));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                return ['success' => true, 'message' => 'Video deleted successfully'];
            } else {
                Log::error('Backblaze delete error: ' . $response);
                return ['success' => false, 'message' => 'Failed to delete video: ' . $response];
            }
        } catch (\Exception $e) {
            Log::error('Error deleting video from Backblaze: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete video by URL
     *
     * @param string $videoUrl Full video URL
     * @return array
     */
    public function deleteVideoByUrl(string $videoUrl): array
    {
        try {
            $cleanUrl = $this->stripAuthorizationQuery($videoUrl);

            // Extract file path from URL
            if (!preg_match('/\/file\/[^\/]+\/(.+)$/', $cleanUrl, $matches)) {
                return ['success' => false, 'message' => 'Could not extract file path from URL'];
            }

            $filePath = urldecode($matches[1]);

            $accountId = env('BACKBLAZE_ACCOUNT_ID');
            $applicationKey = env('BACKBLAZE_APPLICATION_KEY');
            $bucketId = env('BACKBLAZE_BUCKET_ID');

            if (!$accountId || !$applicationKey || !$bucketId) {
                return ['success' => false, 'message' => 'Backblaze credentials not configured'];
            }

            // Authorize with Backblaze
            $authResponse = $this->authorizeAccount($accountId, $applicationKey);
            if (!$authResponse['success']) {
                return ['success' => false, 'message' => 'Failed to authorize with Backblaze'];
            }

            $authToken = $authResponse['authorizationToken'];
            $apiUrl = $authResponse['apiUrl'];

            // List file versions to get file ID
            $ch = curl_init($apiUrl . '/b2api/v2/b2_list_file_versions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $authToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'bucketId' => $bucketId,
                'startFileName' => $filePath,
                'maxFileCount' => 1,
            ]));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                // Find exact match
                $file = null;
                foreach ($data['files'] ?? [] as $f) {
                    if ($f['fileName'] === $filePath) {
                        $file = $f;
                        break;
                    }
                }

                if ($file && isset($file['fileId'])) {
                    // Delete using file ID
                    return $this->deleteVideo($file['fileId'], $filePath);
                } else {
                    return ['success' => false, 'message' => 'File not found in Backblaze'];
                }
            } else {
                return ['success' => false, 'message' => 'Failed to list file versions'];
            }
        } catch (\Exception $e) {
            Log::error('Error deleting video by URL from Backblaze: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
