<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostInteractionController extends Controller
{
    /**
     * Track a post view
     */
    public function trackView(Request $request)
    {
        try {
            $user_id = Auth::id();
            $post_id = $request->post_id;

            $post = Post::find($post_id);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            // Check if user has already viewed this post
            $existingView = PostView::where('user_id', $user_id)
                ->where('post_id', $post_id)
                ->first();

            if (!$existingView) {
                // Create new view record
                PostView::create([
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                    'viewed_at' => now(),
                ]);

                // Increment view count
                $post->increment('view_count');
            }

            return response()->json([
                'message' => 'View tracked successfully',
                'view_count' => $post->fresh()->view_count
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error tracking view'], 500);
        }
    }

    /**
     * Toggle like on a post
     */
    public function toggleLike(Request $request)
    {
        try {
            $user_id = Auth::id();
            $post_id = $request->post_id;

            $post = Post::find($post_id);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $existingLike = PostLike::where('user_id', $user_id)
                ->where('post_id', $post_id)
                ->first();

            if ($existingLike) {
                // Unlike the post
                $existingLike->delete();
                $post->decrement('like_count');
                $isLiked = false;
            } else {
                // Like the post
                PostLike::create([
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                ]);
                $post->increment('like_count');
                $isLiked = true;
            }

            return response()->json([
                'message' => $isLiked ? 'Post liked successfully' : 'Post unliked successfully',
                'like_count' => $post->fresh()->like_count,
                'is_liked' => $isLiked
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error toggling like'], 500);
        }
    }

    /**
     * Get post interaction stats
     */
    public function getPostStats(Request $request, $post_id)
    {
        try {
            $post = Post::find($post_id);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $user_id = Auth::id();
            $isLiked = PostLike::where('user_id', $user_id)
                ->where('post_id', $post_id)
                ->exists();

            return response()->json([
                'view_count' => $post->view_count,
                'like_count' => $post->like_count,
                'is_liked' => $isLiked
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error fetching stats'], 500);
        }
    }
}
