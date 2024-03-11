<?php

namespace App\Http\Controllers;

use App\Enums\CategoryGuardName;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Requests\StorePostCarRequest;
use App\Http\Requests\StorePostFashionRequest;
use App\Http\Requests\StorePostHousesApartmentRequest;
use App\Http\Requests\StorePostLandPlotRequest;
use App\Http\Requests\StorePostMobileRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostCar;
use App\Models\PostFashion;
use App\Models\PostHousesApartment;
use App\Models\PostLandPlot;
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
            'type' => $request->post_type,
            'status' => PostStatus::Pending,
        ]);

        // Store details based on category
        switch ($request->guard_name) {
            case CategoryGuardName::Cars->value: // Assuming 1 is the category ID for cars
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
            case CategoryGuardName::HousesApartments->value: // Assuming 2 is the category ID for properties
                PostHousesApartment::create([
                    'post_id' => $request->post_id,
                    'type' => $request->type,
                    'bedrooms' => $request->bedrooms,
                    'furnishing' => $request->furnishing,
                    'construction_status' => $request->construction_status,
                    'listed_by' => $request->listed_by,
                    'super_builtup_area' => $request->super_builtup_area,
                    'carpet_area' => $request->carpet_area,
                    'monthly_maintenance' => $request->monthly_maintenance,
                    'total_floors' => $request->total_floors,
                    'floor_no' => $request->floor_no,
                    'car_parking' => $request->car_parking,
                    'facing' => $request->facing,
                    'project_name' => $request->project_name,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::LandPlots->value: // Assuming 3 is the category ID for mobiles
                PostLandPlot::create([
                    'post_id' => $request->post_id,
                    'listed_by' => $request->listed_by,
                    'carpet_area' => $request->carpet_area,
                    'length' => $request->length,
                    'breadth' => $request->breadth,
                    'facing' => $request->facing,
                    'project_name' => $request->project_name,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
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
            case CategoryGuardName::HousesApartments->value:
                return (new StorePostHousesApartmentRequest())->rules();
            case CategoryGuardName::LandPlots->value:
                return (new StorePostLandPlotRequest())->rules();
            case CategoryGuardName::Fashion->value:
                return (new StorePostFashionRequest())->rules();
            case CategoryGuardName::Mobiles->value:
                return (new StorePostMobileRequest())->rules();
            default:
                return [
                    'guard_name' => ['required', 'string', Rule::in(CategoryGuardName::allTypes())],
                    'post_type' => ['required', 'string', Rule::in(PostType::allTypes())],
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
