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
        $posts = Post::with('user', 'category')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $posts->where('status', $request->status);
        } else {
            $posts->where('status', PostStatus::Pending);
        }
        $posts = $posts->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category']);

        // Load post details based on category
        $postDetails = null;
        if ($post->category) {
            $categoryName = strtolower($post->category->name);
            switch ($categoryName) {
                case 'cars':
                    $postDetails = $post->postCar;
                    break;
                case 'bikes':
                    $postDetails = $post->postBike;
                    break;
                case 'mobile':
                    $postDetails = $post->postMobile;
                    break;
                case 'houses & apartments':
                    $postDetails = $post->postHousesApartment;
                    break;
                case 'land & plots':
                    $postDetails = $post->postLandPlot;
                    break;
                case 'shop & offices':
                    $postDetails = $post->postShopOffice;
                    break;
                case 'pg & guest houses':
                    $postDetails = $post->postPgGuestHouse;
                    break;
                case 'furniture':
                    $postDetails = $post->postFurniture;
                    break;
                case 'fashion':
                    $postDetails = $post->postFashion;
                    break;
                case 'pets':
                    $postDetails = $post->postPet;
                    break;
                case 'other':
                    $postDetails = $post->postOther;
                    break;
                case 'services':
                    $postDetails = $post->postService;
                    break;
                case 'electronics & appliances':
                    $postDetails = $post->postElectronicsAppliance;
                    break;
                case 'books':
                    $postDetails = $post->postBook;
                    break;
                case 'accessories':
                    $postDetails = $post->postAccessories;
                    break;
                case 'sport & hobbies':
                    $postDetails = $post->postSportHobby;
                    break;
                case 'vehicle spare parts':
                    $postDetails = $post->postVehicleSpareParts;
                    break;
                case 'heavy machinery':
                    $postDetails = $post->postHeavyMachinery;
                    break;
                case 'heavy vehicles':
                    $postDetails = $post->postHeavyVehicle;
                    break;
            }
        }

        return view('admin.posts.show', compact('post', 'postDetails'));
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

    public function report(Request $request, Post $post)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        // Here you would typically save the report to a reports table
        // For now, we'll just update the post status to blocked
        $post->update([
            'status' => 'blocked',
            'report_reason' => $request->reason,
            'report_description' => $request->description,
        ]);

        return back()->with('success', 'Post has been reported and blocked.');
    }
}
