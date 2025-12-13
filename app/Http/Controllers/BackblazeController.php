<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
