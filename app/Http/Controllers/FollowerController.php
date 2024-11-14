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
            return response()->json(['message' => 'Successfully followed the user.'], 201);
        }

        return response()->json(['message' => 'Already following this user.'], 409);
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
            return response()->json(['message' => 'Successfully followed the post.'], 201);
        }

        return response()->json(['message' => 'Already following this post.'], 409);
    }

    public function unfollowUser(Request $request)
    {
        $user = auth()->user(); // Authenticated user
        $targetUserId = $request->user_id; // User to unfollow

        if ($user->following()->where('user_id', $targetUserId)->exists()) {
            $user->following()->detach($targetUserId);
            return response()->json(['message' => 'Successfully unfollowed the user.'], 200);
        }

        return response()->json(['message' => 'You are not following this user.'], 404);
    }

    // Unfollow a post
    public function unfollowPost(Request $request)
    {
        $user = auth()->user(); // Authenticated user
        $postId = $request->post_id; // Post to unfollow

        if ($user->followedPosts()->where('post_id', $postId)->exists()) {
            $user->followedPosts()->detach($postId);
            return response()->json(['message' => 'Successfully unfollowed the post.'], 200);
        }

        return response()->json(['message' => 'You are not following this post.'], 404);
    }
}
