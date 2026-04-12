<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostContentType;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostVideoController extends Controller
{
    /**
     * Active posts that include at least one video (post_contents).
     * Optional query: category (same parent/subcategory rules as posts index), limit (per page, default 15).
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'category' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Post::query()
            ->select(['posts.id', 'posts.category_id', 'posts.title', 'posts.post_time'])
            ->where('posts.status', PostStatus::Active)
            ->whereHas('contents', function ($q) {
                $q->where('type', PostContentType::Video->value);
            });

        if ($request->filled('category')) {
            $category = (int) $request->input('category');
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

        $perPage = (int) $request->input('limit', 15);

        $query->with(['contents' => function ($q) {
            $q->where('type', PostContentType::Video->value)->orderBy('sort_order')->limit(1);
        }]);

        $paginator = $query
            ->orderByDesc('posts.post_time')
            ->simplePaginate($perPage);

        return $paginator->through(function (Post $post) {
            $videoRow = $post->contents->first();
            $rawUrl = $videoRow && $videoRow->url ? (string) $videoRow->url : '';

            return [
                'category_id' => $post->category_id,
                'post_id' => $post->id,
                'title' => $post->title,
                'video_url' => $rawUrl,
            ];
        });
    }
}
