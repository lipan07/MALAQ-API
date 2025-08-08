<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        // Fetch posts with related data and paginate
        $posts = Post::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function approve($id)
    {
        $post = Post::findOrFail($id);
        $post->status = 'approved'; // Assuming your PostStatus enum allows this
        $post->save();

        return redirect()->back()->with('success', 'Post approved successfully.');
    }
}
