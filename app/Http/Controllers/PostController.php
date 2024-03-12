<?php

namespace App\Http\Controllers;

use App\Enums\CategoryGuardName;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Requests\StorePgGuestHouseRequest;
use App\Http\Requests\StorePostAccessoriesRequest;
use App\Http\Requests\StorePostBikeRequest;
use App\Http\Requests\StorePostBookRequest;
use App\Http\Requests\StorePostCarRequest;
use App\Http\Requests\StorePostElectronicsApplianceRequest;
use App\Http\Requests\StorePostFashionRequest;
use App\Http\Requests\StorePostFurnitureRequest;
use App\Http\Requests\StorePostHeavyMachineryRequest;
use App\Http\Requests\StorePostHeavyVehicleRequest;
use App\Http\Requests\StorePostHousesApartmentRequest;
use App\Http\Requests\StorePostJobRequest;
use App\Http\Requests\StorePostLandPlotRequest;
use App\Http\Requests\StorePostMobileRequest;
use App\Http\Requests\StorePostOtherRequest;
use App\Http\Requests\StorePostPetRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StorePostServiceRequest;
use App\Http\Requests\StorePostSportHobbyRequest;
use App\Http\Requests\StoreShopOfficeRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostAccessories;
use App\Models\PostBike;
use App\Models\PostBook;
use App\Models\PostCar;
use App\Models\PostElectronicsAppliance;
use App\Models\PostFashion;
use App\Models\PostFurniture;
use App\Models\PostHeavyMachinery;
use App\Models\PostHeavyVehicle;
use App\Models\PostHousesApartment;
use App\Models\PostJob;
use App\Models\PostLandPlot;
use App\Models\PostMobile;
use App\Models\PostOther;
use App\Models\PostPet;
use App\Models\PostPgGuestHouse;
use App\Models\PostProperty;
use App\Models\PostService;
use App\Models\PostShopOffice;
use App\Models\PostSportHobby;
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
                    'post_id' => $post->id,
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
                    'post_id' => $post->id,
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
            case CategoryGuardName::Mobiles->value: // Assuming 3 is the category ID for mobiles
                $postMobile = PostMobile::create([
                    'post_id' => $post->id,
                    'brand' => $request->brand,
                    'year' => $request->year,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Bikes->value: // Assuming 3 is the category ID for mobiles
                $postBike = PostBike::create([
                    'post_id' => $post->id,
                    'brand' => $request->brand,
                    'year' => $request->year,
                    'km_driven' => $request->km_driven,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Job->value: // Assuming 3 is the category ID for mobiles
                $postJob = PostJob::create([
                    'post_id' => $post->id,
                    'salary_period' => $request->salary_period,
                    'position_type' => $request->position_type,
                    'salary_from' => $request->salary_from,
                    'salary_to' => $request->salary_to,
                    'title' => $request->title,
                    'description' => $request->description,
                ]);
                break;
            case CategoryGuardName::Pets->value: // Assuming 3 is the category ID for mobiles
                $postPet = PostPet::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Furniture->value: // Assuming 3 is the category ID for mobiles
                $postFurniture = PostFurniture::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Fashion->value: // Assuming 3 is the category ID for mobiles
                $postFashion = PostFashion::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::ElectronicsAppliances->value: // Assuming 3 is the category ID for mobiles
                $postElectronicsAppliance = PostElectronicsAppliance::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Others->value: // Assuming 3 is the category ID for mobiles
                $postOther = PostOther::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::ShopOffices->value: // Assuming 3 is the category ID for mobiles
                $postShopOffice = PostShopOffice::create([
                    'post_id' => $post->id,
                    'furnishing' => $request->furnishing,
                    'listed_by' => $request->listed_by,
                    'super_builtup_area' => $request->super_builtup_area,
                    'carpet_area' => $request->carpet_area,
                    'monthly_maintenance' => $request->monthly_maintenance,
                    'car_parking' => $request->car_parking,
                    'washroom' => $request->washroom,
                    'project_name' => $request->project_name,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::PgGuestHouses->value: // Assuming 3 is the category ID for mobiles
                $postPgGuestHouse = PostPgGuestHouse::create([
                    'post_id' => $post->id,
                    'type' => $request->type,
                    'furnishing' => $request->furnishing,
                    'listed_by' => $request->listed_by,
                    'carpet_area' => $request->carpet_area,
                    'is_meal_included' => $request->is_meal_included,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Accessories->value: // Assuming 3 is the category ID for mobiles
                $postAccessory = PostAccessories::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::CommercialHeavyVehicles->value: // Assuming 3 is the category ID for mobiles
                $postHeavyVehicle = PostHeavyVehicle::create([
                    'post_id' => $post->id,
                    'title' => $request->title,
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'year' => $request->year,
                    'condition' => $request->condition,
                    'km_driven' => $request->km_driven,
                    'fuel_type' => $request->fuel_type,
                    'price' => $request->price,
                    'description' => $request->description,
                    'contact_name' => $request->contact_name,
                    'contact_phone' => $request->contact_phone,
                ]);
                break;
            case CategoryGuardName::CommercialHeavyMachinery->value: // Assuming 3 is the category ID for mobiles
                $postHeavyMachinery = PostHeavyMachinery::create([
                    'post_id' => $request->post_id,
                    'title' => $request->title,
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'year' => $request->year,
                    'condition' => $request->condition,
                    'hours_used' => $request->hours_used,
                    'description' => $request->description,
                    'price' => $request->price,
                    'contact_name' => $request->contact_name,
                    'contact_phone' => $request->contact_phone,
                ]);
                break;
            case CategoryGuardName::Books->value: // Assuming 3 is the category ID for mobiles
                $postBook = PostBook::create([
                    'post_id' => $request->post_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::SportsInstrument->value: // Assuming 3 is the category ID for mobiles
                $postSportHobby = PostSportHobby::create([
                    'post_id' => $request->post_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'amount' => $request->amount,
                ]);
                break;
            case CategoryGuardName::Services->value: // Assuming 3 is the category ID for mobiles
                $postService = PostService::create([
                    'post_id' => $request->post_id,
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
            case CategoryGuardName::Bikes->value:
                return (new StorePostBikeRequest())->rules();
            case CategoryGuardName::Job->value:
                return (new StorePostJobRequest())->rules();
            case CategoryGuardName::Pets->value:
                return (new StorePostPetRequest())->rules();
            case CategoryGuardName::Furniture->value:
                return (new StorePostFurnitureRequest())->rules();
            case CategoryGuardName::ElectronicsAppliances->value:
                return (new StorePostElectronicsApplianceRequest())->rules();
            case CategoryGuardName::Others->value:
                return (new StorePostOtherRequest())->rules();
            case CategoryGuardName::ShopOffices->value:
                return (new StoreShopOfficeRequest())->rules();
            case CategoryGuardName::PgGuestHouses->value:
                return (new StorePgGuestHouseRequest())->rules();
            case CategoryGuardName::Accessories->value:
                return (new StorePostAccessoriesRequest())->rules();
            case CategoryGuardName::CommercialHeavyVehicles->value:
                return (new StorePostHeavyVehicleRequest())->rules();
            case CategoryGuardName::CommercialHeavyMachinery->value:
                return (new StorePostHeavyMachineryRequest())->rules();
            case CategoryGuardName::Books->value:
                return (new StorePostBookRequest())->rules();
            case CategoryGuardName::SportsInstrument->value:
                return (new StorePostSportHobbyRequest())->rules();
            case CategoryGuardName::Services->value:
                return (new StorePostServiceRequest())->rules();
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
