<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostContentType;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

class PostVideoController extends Controller
{
    /**
     * Video reel feed: same filter dimensions as GET /posts (category, search, location+radius,
     * listingType, priceRange, sortBy) plus video content constraint.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'category' => 'nullable',
            'limit' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'distance' => 'nullable|numeric|min:0|max:500',
            'listingType' => 'nullable|string|max:32',
            'sortBy' => 'nullable|string|max:64',
            'priceRange' => 'nullable|array',
            'priceRange.*' => 'nullable',
        ]);

        $perPage = (int) $request->input('limit', 15);

        $hasLocation = $request->filled('latitude') && $request->filled('longitude');

        $query = $this->baseVideoQuery();

        $this->applyCategoryFilter($query, $request);
        $this->applySearchFilter($query, $request);
        $this->applyListingTypeFilter($query, $request);
        $this->applyPriceRangeFilter($query, $request);

        if ($hasLocation) {
            $latitude = (float) $request->latitude;
            $longitude = (float) $request->longitude;
            $requestedDistance = (float) ($request->distance ?? 5);

            $query->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(`posts`.`latitude`)) * '
                . 'cos(radians(`posts`.`longitude`) - radians(?)) + '
                . 'sin(radians(?)) * sin(radians(`posts`.`latitude`)))) AS distance',
                [$latitude, $longitude, $latitude]
            )->having('distance', '<=', $requestedDistance);

            $this->applySortForLocationMode($query, $request);
        } else {
            $this->applySortGeneric($query, $request);
        }

        $paginator = $query->with(['contents' => function ($q) {
            $q->where('type', PostContentType::Video->value)->orderBy('sort_order')->limit(1);
        }])->simplePaginate($perPage);

        $postIds = $paginator->getCollection()->pluck('id')->all();
        $userId = auth()->id();
        $likedIds = [];
        if ($userId !== null && $postIds !== []) {
            $likedIds = PostLike::query()
                ->where('user_id', $userId)
                ->whereIn('post_id', $postIds)
                ->pluck('post_id')
                ->all();
        }

        return $paginator->through(function (Post $post) use ($likedIds) {
            $videoRow = $post->contents->first();
            $rawUrl = $videoRow && $videoRow->url ? (string) $videoRow->url : '';
            $typeVal = $post->type instanceof \BackedEnum ? $post->type->value : (string) $post->type;

            return [
                'category_id' => $post->category_id,
                'post_id' => $post->id,
                'user_id' => $post->user_id,
                'title' => $post->title,
                'video_url' => $rawUrl,
                'amount' => $post->amount,
                'listing_type' => $typeVal,
                'like_count' => (int) ($post->like_count ?? 0),
                'is_liked' => in_array($post->id, $likedIds, true),
            ];
        });
    }

    private function baseVideoQuery()
    {
        return Post::query()
            ->select([
                'posts.id',
                'posts.category_id',
                'posts.title',
                'posts.post_time',
                'posts.amount',
                'posts.type',
                'posts.like_count',
                'posts.user_id',
                'posts.latitude',
                'posts.longitude',
            ])
            ->where('posts.status', PostStatus::Active)
            ->whereHas('contents', function ($q) {
                $q->where('type', PostContentType::Video->value);
            });
    }

    private function applyCategoryFilter($query, Request $request): void
    {
        if (!$request->filled('category')) {
            return;
        }

        $category = $request->input('category');
        if ($category === 'donate') {
            $query->where('posts.type', PostType::Donate->value);

            return;
        }

        if (!is_numeric($category)) {
            return;
        }

        $category = (int) $category;
        if (!in_array($category, [1, 7, 76], true)) {
            $hasSubCategories = Category::where('parent_id', $category)->exists();
            if ($hasSubCategories) {
                $subCategoryIds = Category::where('parent_id', $category)->pluck('id')->toArray();
                $query->whereIn('posts.category_id', $subCategoryIds);
            } else {
                $query->where('posts.category_id', $category);
            }
        } else {
            $query->where('posts.category_id', $category);
        }
    }

    private function applySearchFilter($query, Request $request): void
    {
        if (!$request->filled('search')) {
            return;
        }
        $term = trim((string) $request->input('search'));
        if ($term !== '') {
            $query->where('posts.title', 'like', '%' . $term . '%');
        }
    }

    private function applyListingTypeFilter($query, Request $request): void
    {
        if (!$request->filled('listingType')) {
            return;
        }
        $query->where('posts.type', $request->listingType ?? PostType::defaultType()->value);
    }

    private function applyPriceRangeFilter($query, Request $request): void
    {
        if (!$request->filled('priceRange') || !is_array($request->priceRange) || count($request->priceRange) < 2) {
            return;
        }
        $minPrice = $request->priceRange[0];
        $maxPrice = $request->priceRange[1];
        if (!empty($minPrice) && is_numeric($minPrice)) {
            $query->where('posts.amount', '>=', (float) $minPrice);
        }
        if (!empty($maxPrice) && is_numeric($maxPrice)) {
            $query->where('posts.amount', '<=', (float) $maxPrice);
        }
    }

    private function applySortForLocationMode($query, Request $request): void
    {
        if ($request->filled('sortBy')) {
            $sortBy = $request->sortBy;
            switch ($sortBy) {
                case 'Recently Added':
                case 'createdAt_desc':
                    $query->orderByDesc('posts.post_time');
                    break;
                case 'Price: Low to High':
                case 'price_asc':
                    $query->orderByRaw('CAST(posts.amount AS DECIMAL(15,2)) asc');
                    break;
                case 'Price: High to Low':
                case 'price_desc':
                    $query->orderByRaw('CAST(posts.amount AS DECIMAL(15,2)) desc');
                    break;
                case 'Relevance':
                default:
                    $query->orderByDesc('posts.post_time');
                    break;
            }
            $query->where('posts.amount', '>', 0);
        } else {
            $query->orderByDesc('posts.post_time');
        }
    }

    private function applySortGeneric($query, Request $request): void
    {
        if ($request->filled('sortBy')) {
            $sortBy = $request->sortBy;
            switch ($sortBy) {
                case 'Recently Added':
                case 'createdAt_desc':
                    $query->orderByDesc('posts.post_time');
                    break;
                case 'Price: Low to High':
                case 'price_asc':
                    $query->orderByRaw('CAST(posts.amount AS DECIMAL(15,2)) asc');
                    break;
                case 'Price: High to Low':
                case 'price_desc':
                    $query->orderByRaw('CAST(posts.amount AS DECIMAL(15,2)) desc');
                    break;
                case 'Relevance':
                default:
                    $query->orderByDesc('posts.post_time');
                    break;
            }
        } else {
            $query->orderByDesc('posts.post_time');
        }
    }
}
