<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Fetch posts with related data and paginate
        $posts = Post::with('user', 'category', 'images')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $posts->where('status', $request->status);
        } else {
            $posts->where('status', PostStatus::Pending);
        }
        $posts = $posts->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function changeStatus(Request $request, Post $post)
    {
        $statuses = ['pending', 'processing', 'active', 'inactive', 'failed', 'sold', 'blocked'];

        $request->validate([
            'status' => 'required|in:' . implode(',', $statuses),
        ]);

        $post->update(['status' => $request->status]);

        return back()->with('success', "Post status updated to {$request->status}.");
    }
}
