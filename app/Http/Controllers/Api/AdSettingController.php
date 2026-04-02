<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdSetting;
use Illuminate\Http\JsonResponse;

class AdSettingController extends Controller
{
    /**
     * Public read for the mobile app (no auth). Cached client-side for 6 hours.
     */
    public function index(): JsonResponse
    {
        $rows = AdSetting::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['slug', 'label', 'is_enabled', 'sort_order']);

        return response()->json([
            'data' => $rows,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
