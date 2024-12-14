<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

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

    // Logged-in user follows a post
    public function followPost(Request $request)
    {
        $user = auth()->user(); // Authenticated user
        $postId = $request->post_id; // Post to follow

        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        if (!$user->followedPosts()->where('post_id', $postId)->exists()) {
            $user->followedPosts()->attach($postId);
            return response()->json(['message' => 'Followed successfully'], 201);
        } else {
            $user->followedPosts()->detach($postId);
            return response()->json(['message' => 'Unfollowed successfully'], 200);
        }
    }
}
