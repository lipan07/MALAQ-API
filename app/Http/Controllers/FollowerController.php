<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostFollower;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    public function followUser(Request $request)
    {
        $user = auth()->user(); // Authenticated user
        $targetUserId = $request->user_id; // User to follow

        if (!$user->following()->where('user_id', $targetUserId)->exists()) {
            $user->following()->attach($targetUserId);
            return response()->json(['message' => 'Followed successfully'], 201);
        } else {
            $user->following()->detach($targetUserId);
            return response()->json(['message' => 'Unfollowed successfully'], 200);
        }
    }

    public function followPost(Request $request)
    {
        try {
            $user_id = Auth::id();
            $post_id = $request->post_id;
            $post = Post::find($post_id);

            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $follow = PostFollower::where('user_id', $user_id)
                ->where('post_id', $post_id)
                ->first();

            if ($follow) {
                // If already following, unfollow the post
                $follow->delete();
                return response()->json(['message' => 'Unfollowed successfully']);
            } else {
                // If not following, follow the post
                $follow = PostFollower::create([
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                    'post_user_id' => $post->user_id
                ]);
                return response()->json(['message' => 'Followed successfully', 'follow' => $follow]);
            }
        } catch (Exception $e) {
            \Log::error($e->getMessage());
        }
    }


    public function getFollowers($post_id)
    {
        $followers = PostFollower::where('post_id', $post_id)->with('user')->get();
        return response()->json($followers);
    }

    public function getFollowingPosts()
    {
        $user_id = Auth::id();
        $posts = PostFollower::where('user_id', $user_id)->with('post')->get();
        return response()->json($posts);
    }

    public function getAllFollowersForMyPosts()
    {
        $user_id = Auth::id(); // Get the ID of the logged-in user

        $followers = PostFollower::whereHas('post', function ($query) use ($user_id) {
            $query->where('user_id', $user_id); // Only consider posts created by the user
        })->with('user')->get();

        return response()->json($followers);
    }
}
