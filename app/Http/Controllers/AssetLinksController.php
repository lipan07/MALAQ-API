<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AssetLinksController extends Controller
{
    /**
     * Serve the Digital Asset Links JSON file for Android App Links verification
     * 
     * This file must be accessible at: https://nearx.co/.well-known/assetlinks.json
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get SHA-256 fingerprints from environment or config
        // You need to add these from Google Play Console
        $sha256Fingerprints = [
            // Add your SHA-256 certificate fingerprints here
            // You can get these from Google Play Console > Your App > Release > Setup > App signing
            // Or from: keytool -list -v -keystore your-keystore.jks -alias your-alias
            env('ANDROID_SHA256_FINGERPRINT_1', ''),
            // Add additional fingerprints if you have multiple signing keys
            // env('ANDROID_SHA256_FINGERPRINT_2', ''),
        ];

        // Filter out empty fingerprints
        $sha256Fingerprints = array_filter($sha256Fingerprints);

        // If no fingerprints are configured, return empty array (will fail verification)
        if (empty($sha256Fingerprints)) {
            return response()->json([], 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        $assetLinks = [
            [
                'relation' => ['delegate_permission/common.handle_all_urls'],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => 'com.malaq.notify', // Your Android app package name
                    'sha256_cert_fingerprints' => array_values($sha256Fingerprints),
                ],
            ],
        ];

        return response()->json($assetLinks, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
        ]);
    }
}

