<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EngloContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EngloContentController extends Controller
{
    /**
     * List Englo contents with simple pagination and optional filters.
     * Public API (no auth required).
     *
     * Query: per_page (default 10), page, genre_id, language_id, podcast_genre_id
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = min(max($perPage, 1), 100);

        $query = EngloContent::query()->orderByDesc('created_at');

        if ($request->filled('podcast_genre_id')) {
            $query->where('podcast_genre_id', (int) $request->input('podcast_genre_id'));
        }
        if ($request->filled('genre_id')) {
            $query->where('genre_id', (int) $request->input('genre_id'));
        }
        if ($request->filled('language_id')) {
            $query->where('language_id', (int) $request->input('language_id'));
        }

        $contents = $query->simplePaginate($perPage);

        $items = $contents->map(function (EngloContent $item) {
            $genre = $item->genre();
            $language = $item->language();
            $podcast = $item->podcastGenre();
            return [
                'id' => $item->id,
                'genre_id' => $item->genre_id,
                'genre' => $genre?->label(),
                'language_id' => $item->language_id,
                'language' => $language?->label(),
                'podcast_genre_id' => $item->podcast_genre_id,
                'podcast' => $podcast?->label(),
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
