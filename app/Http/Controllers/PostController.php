<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostCar;
use App\Models\PostMobile;
use App\Models\PostProperty;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data

        // Create the post
        $post = Post::create([
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'post_time' => now(),
        ]);

        // Store details based on category
        switch ($request->category_id) {
            case 1: // Assuming 1 is the category ID for cars
                PostCar::create([
                    'post_id' => $post->id,
                    // Add car-specific details
                ]);
                break;
            case 2: // Assuming 2 is the category ID for properties
                PostProperty::create([
                    'post_id' => $post->id,
                    // Add property-specific details
                ]);
                break;
            case 3: // Assuming 3 is the category ID for mobiles
                PostMobile::create([
                    'post_id' => $post->id,
                    // Add mobile-specific details
                ]);
                break;
        }

        return response()->json(['message' => 'Post created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
