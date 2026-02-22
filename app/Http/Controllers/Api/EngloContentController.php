<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EngloContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EngloContentController extends Controller
{
    /**
     * List Englo contents with simple pagination.
     * Public API (no auth required).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        $contents = EngloContent::orderByDesc('created_at')
            ->simplePaginate($perPage);

        $items = $contents->map(function (EngloContent $item) {
            return [
                'id' => $item->id,
                'genre_id' => $item->genre_id,
                'genre' => $item->genre()->label(),
                'language_id' => $item->language_id,
                'language' => $item->language()->label(),
                'video_url' => $item->video_url,
                'data' => $item->data,
                'created_at' => $item->created_at?->toIso8601String(),
                'updated_at' => $item->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $contents->currentPage(),
                'per_page' => $contents->perPage(),
                'has_more_pages' => $contents->hasMorePages(),
                'next_page_url' => $contents->nextPageUrl(),
                'prev_page_url' => $contents->previousPageUrl(),
            ],
        ]);
    }
}
