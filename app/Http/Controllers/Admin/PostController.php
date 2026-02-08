<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('user', 'category')->orderBy('post_time', 'desc');

        // Status filter (optional – when empty, show all)
        if ($request->filled('status') && in_array($request->status, PostStatus::allStatus(), true)) {
            $query->where('status', $request->status);
        }

        // Date range (post_time)
        if ($request->filled('date_from')) {
            $from = Carbon::parse($request->date_from)->startOfDay();
            $query->where('post_time', '>=', $from);
        }
        if ($request->filled('date_to')) {
            $to = Carbon::parse($request->date_to)->endOfDay();
            $query->where('post_time', '<=', $to);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search: title, user email, category name
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('email', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($term) {
                        $cq->where('name', 'like', "%{$term}%");
                    });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $posts = $query->paginate($perPage)->withQueryString();

        // Only categories that have posts, with parent for display (avoids duplicate-looking names)
        $categories = Category::with('parent')
            ->whereHas('posts')
            ->orderBy('name')
            ->get();

        return view('admin.posts.index', compact('posts', 'perPage', 'categories'));
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
        if (!request()->user()->canApproveListings()) {
            abort(403, 'You do not have permission to change listing status.');
        }
        $statuses = ['pending', 'processing', 'active', 'inactive', 'failed', 'sold', 'blocked'];

        $request->validate([
            'status' => 'required|in:' . implode(',', $statuses),
        ]);

        $post->update(['status' => $request->status]);

        return back()->with('success', "Post status updated to {$request->status}.");
    }

    public function report(Request $request, Post $post)
    {
        if (!request()->user()->canApproveListings()) {
            abort(403, 'You do not have permission to report/block listings.');
        }
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
