<?php

namespace App\Http\Controllers;

use App\Enums\CategoryGuardName;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Requests\StorePostCarRequest;
use App\Http\Requests\StorePostFashionRequest;
use App\Http\Requests\StorePostMobileRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostCar;
use App\Models\PostFashion;
use App\Models\PostMobile;
use App\Models\PostProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $rules = $this->getValidationRules($request->guard_name);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create the post
        $post = Post::create([
            'category_id' => Category::getIdByGuardName($request->guard_name),
            'user_id' => auth()->id(),
            'post_time' => now(),
            'address' => $request->address,
            'latitude' => $request->lattitude,
            'longitude' => $request->longitude,
            'type' => $request->type,
            'status' => PostStatus::Pending,
        ]);

        // Store details based on category
        switch ($request->guard_name) {
            case 'cars': // Assuming 1 is the category ID for cars
                PostCar::create([
                    'post_id' => $post->id,
                    'brand' => $request->brand,
                    'year' => $request->year,
                    'fuel' => $request->fuel,
                    'transmission' => $request->transmission,
                    'km_driven' => $request->km_driven,
                    'no_of_owner' => $request->no_of_owner,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    // Other car-specific fields...
                ]);
                break;
            case 2: // Assuming 2 is the category ID for properties
                PostFashion::create([
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

    protected function getValidationRules($guardName)
    {
        switch ($guardName) {
            case CategoryGuardName::Cars->value:
                return (new StorePostCarRequest())->rules();
            case CategoryGuardName::Fashion->value:
                return (new StorePostFashionRequest())->rules();
            case CategoryGuardName::Mobiles->value:
                return (new StorePostMobileRequest())->rules();
            default:
                return [
                    'guard_name' => ['required', 'string', Rule::in(CategoryGuardName::allTypes())],
                    'type' => ['required', 'string', Rule::in(PostType::allTypes())],
                ];
        }
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
