<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostVideoController extends Controller
{
    /**
     * Active posts that include at least one video URL.
     * Optional query: category (same parent/subcategory rules as posts index), limit (per page, default 15).
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'category' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Post::query()
            ->select(['id', 'category_id', 'title', 'videos', 'post_time'])
            ->where('status', PostStatus::Active)
            ->whereNotNull('videos')
            ->whereJsonLength('videos', '>', 0);

        if ($request->filled('category')) {
            $category = (int) $request->input('category');
            if (!in_array($category, [1, 7, 76], true)) {
                $hasSubCategories = Category::where('parent_id', $category)->exists();
                if ($hasSubCategories) {
                    $subCategoryIds = Category::where('parent_id', $category)->pluck('id')->toArray();
                    $query->whereIn('category_id', $subCategoryIds);
                } else {
                    $query->where('category_id', $category);
                }
            } else {
                $query->where('category_id', $category);
            }
        }

        $perPage = (int) $request->input('limit', 15);

        $paginator = $query
            ->orderByDesc('post_time')
            ->simplePaginate($perPage);

        return $paginator->through(function (Post $post) {
            $urls = array_values(array_filter(
                $post->videos ?? [],
                static fn ($u) => is_string($u) && $u !== ''
            ));
            $rawUrl = $urls[0] ?? '';

            return [
                'category_id' => $post->category_id,
                'post_id' => $post->id,
                'title' => $post->title,
                'video_url' => $rawUrl,
            ];
        });
    }
}
