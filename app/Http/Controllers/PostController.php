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
        $posts = Post::with('category')->get();

        foreach ($posts as $post) {
            $categoryGuardName = Category::getGuardNameById($post->category_id);
            switch ($categoryGuardName) {
                case 'mobiles':
                    $post->load('mobile');
                    break;

                case 'cars':
                    $post->load('car');
                    break;

                    // Add more cases for other categories if needed
            }
        }
        return $posts;
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
        $modelMapping = [
            CategoryGuardName::Cars->value => PostCar::class,
            CategoryGuardName::HousesApartments->value => PostHousesApartment::class,
            CategoryGuardName::LandPlots->value => PostLandPlot::class,
            CategoryGuardName::Mobiles->value => PostMobile::class,
            CategoryGuardName::Bikes->value => PostBike::class,
            CategoryGuardName::Job->value => PostJob::class,
            CategoryGuardName::Pets->value => PostPet::class,
            CategoryGuardName::Furniture->value => PostFurniture::class,
            CategoryGuardName::Fashion->value => PostFashion::class,
            CategoryGuardName::ElectronicsAppliances->value => PostElectronicsAppliance::class,
            CategoryGuardName::Others->value => PostOther::class,
            CategoryGuardName::ShopOffices->value => PostShopOffice::class,
            CategoryGuardName::PgGuestHouses->value => PostPgGuestHouse::class,
            CategoryGuardName::Accessories->value => PostAccessories::class,
            CategoryGuardName::CommercialHeavyVehicles->value => PostHeavyVehicle::class,
            CategoryGuardName::CommercialHeavyMachinery->value => PostHeavyMachinery::class,
            CategoryGuardName::Books->value => PostBook::class,
            CategoryGuardName::SportsInstrument->value => PostSportHobby::class,
            CategoryGuardName::Services->value => PostService::class,
        ];

        $modelClass = $modelMapping[$request->guard_name] ?? null;

        if ($modelClass) {
            $request->merge(['post_id' => $post->id]);
            $modelClass::create(array_merge($request->all()));
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
