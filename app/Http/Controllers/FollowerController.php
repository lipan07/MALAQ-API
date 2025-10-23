<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{

    public function followPost(Request $request)
    {
        try {
            $user_id = Auth::id();
            $post_id = $request->post_id;
            $post = Post::find($post_id);

            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $like = PostLike::where('user_id', $user_id)
                ->where('post_id', $post_id)
                ->first();

            if ($like) {
                // If already liked, unlike the post
                $like->delete();
                $post->decrement('like_count');
                return response()->json([
                    'message' => 'Unliked successfully',
                    'like_count' => $post->fresh()->like_count,
                    'is_liked' => false
                ]);
            } else {
                // If not liked, like the post
                PostLike::create([
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                ]);
                $post->increment('like_count');
                return response()->json([
                    'message' => 'Liked successfully',
                    'like_count' => $post->fresh()->like_count,
                    'is_liked' => true
                ]);
            }
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error processing request'], 500);
        }
    }


    public function postLikesByPostID($post_id)
    {
        $likes = PostLike::where('post_id', $post_id)->with('user', 'user.images')->get();
        return response()->json($likes);
    }

    public function userLikedPosts()
    {
        $user_id = Auth::id();
        $posts = PostLike::where('user_id', $user_id)
            ->with([
                'post' => function ($query) {
                    $query->with([
                        'images',
                    'user:id,name,phone_no,status,last_activity,created_at',
                    'category:id,name,parent_id',
                    'mobile',
                    'car',
                    'housesApartment',
                    'landPlots',
                    'fashion',
                    'bikes',
                    'jobs',
                    'pets'
                    ]);
                }
            ])
            ->get();
        return response()->json($posts);
    }

    public function postLikes()
    {
        $user_id = Auth::id(); // Get the ID of the logged-in user

        $likes = PostLike::whereHas('post', function ($query) use ($user_id) {
            $query->where('user_id', $user_id); // Only consider posts created by the user
        })->with('user', 'post', 'post.images')->get();

        return response()->json($likes);
    }

    /**
     * Follow a user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followUser(Request $request)
    {
        $request->validate([
            // 'follower_id' => 'required|exists:users,id',
            'following_id' => 'required|exists:users,id',
        ]);

        $follower = Auth::user();
        $followingId = $request->following_id;

        // Prevent following self
        if ($follower->id === $followingId) {
            return response()->json(['message' => 'You cannot follow yourself.'], 400);
        }

        // Check if already following
        if (!$follower->following()->where('following_id', $followingId)->exists()) {
            $follower->following()->attach($followingId);
            return response()->json(['message' => 'Followed successfully.'], 201);
        } else {
            $follower->following()->detach($followingId);
            return response()->json(['message' => 'Unfollowed successfully'], 200);
        }

        return response()->json(['message' => 'You are already following this user.'], 400);
    }

    /**
     * Get all followers of the logged-in user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userFollowers()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $followers = $user->followers()
            ->with('images:id,imageable_id,url') // Include the images relationship
            ->select('id', 'name', 'email', 'address', 'latitude', 'longitude', 'about_me')
            ->get();

        return response()->json([
            'followers' => $followers,
        ]);
    }

    /**
     * Get all users the logged-in user is following.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userFollowing()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $following = $user->following()
            ->with('images:id,imageable_id,url') // Include the images relationship
            ->select('id', 'name', 'email', 'address', 'latitude', 'longitude', 'about_me')
            ->get();

        return response()->json([
            'following' => $following,
        ]);
    }
}
