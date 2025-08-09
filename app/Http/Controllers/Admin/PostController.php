<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        // Fetch posts with related data and paginate
        $posts = Post::orderBy('created_at', 'desc')
            ->where('status', PostStatus::Pending)
            ->paginate(10);

        $posts->load([
            'user',
            'category',
            'images',
        ]);

        // $posts = Post::with(['user', 'category', 'images'])->orderBy('created_at', 'desc')->paginate(10);


        return view('admin.posts.index', compact('posts'));
    }

    public function approve($id)
    {
        $post = Post::findOrFail($id);
        $post->status = PostStatus::Active; // Assuming your PostStatus enum allows this
        $post->save();

        return redirect()->back()->with('success', 'Post approved successfully.');
    }
}
