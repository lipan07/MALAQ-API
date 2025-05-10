<?php

namespace App\Http\Controllers;

use App\Enums\CategoryGuardName;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Requests\FetchPostsRequest;
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
use App\Http\Requests\StorePostVehicleSparePartsRequest;
use App\Http\Requests\StoreServicePostRequest;
use App\Http\Requests\StoreShopOfficeRequest;
use App\Http\Requests\UpdatePgGuestHouseRequest;
use App\Http\Requests\UpdatePostAccessoriesRequest;
use App\Http\Requests\UpdatePostBikeRequest;
use App\Http\Requests\UpdatePostBookRequest;
use App\Http\Requests\UpdatePostCarRequest;
use App\Http\Requests\UpdatePostElectronicsApplianceRequest;
use App\Http\Requests\UpdatePostFashionRequest;
use App\Http\Requests\UpdatePostFurnitureRequest;
use App\Http\Requests\UpdatePostHeavyMachineryRequest;
use App\Http\Requests\UpdatePostHeavyVehicleRequest;
use App\Http\Requests\UpdatePostHousesApartmentRequest;
use App\Http\Requests\UpdatePostJobRequest;
use App\Http\Requests\UpdatePostLandPlotRequest;
use App\Http\Requests\UpdatePostMobileRequest;
use App\Http\Requests\UpdatePostOtherRequest;
use App\Http\Requests\UpdatePostPetRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\UpdatePostServiceRequest;
use App\Http\Requests\UpdatePostSportHobbyRequest;
use App\Http\Requests\UpdatePostVehicleSparePartsRequest;
use App\Http\Requests\UpdateShopOfficeRequest;
use App\Http\Resources\PostResource;
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
use App\Models\PostVehicleSpareParts;
use App\Services\PostService as ServicesPostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FetchPostsRequest $request)
    {
        $userId = Auth::id();

        // Query posts with relationships
        $postsQuery = Post::with([
            'user',
            'category',
            'images',
            'follower' => fn($query) => $query->where('user_id', $userId), // Only include followers for the authenticated user
        ]);

        // Filter by category if provided
        if ($request->filled('category')) {
            if (!in_array($request->category, [1, 7])) {
                $hasSubCategories = Category::where('parent_id', $request->category)->exists();

                if (!$hasSubCategories) {
                    return response()->json([
                        'message' => 'This category does not have any subcategories.',
                        'sub_category_ids' => [],
                    ], 404);
                }

                // Fetch all subcategory IDs
                $subCategoryIds = Category::where('parent_id', $request->category)->pluck('id')->toArray();

                $postsQuery->whereIn('category_id', $subCategoryIds);
            } else {
                $postsQuery->where('category_id', $request->category);
            }
        }

        // Filter by search term if provided
        if ($request->filled('search')) {
            $postsQuery->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Paginate and order results
        $posts = $postsQuery->orderByDesc('created_at')->simplePaginate(15);

        // Fetch additional data for posts
        $posts = ServicesPostService::fetchPostData($posts);

        // Return as resource collection
        return PostResource::collection($posts);
    }

    public function myPost()
    {
        $user = auth()->user();
        $posts = Post::with('user', 'category', 'images')->where(['user_id' => $user->id])->orderBy('created_at', 'DESC')->simplePaginate(6);

        $posts = ServicesPostService::fetchPostData($posts);
        // Return the restructured paginated result

        return PostResource::collection($posts);
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
    public function store(StorePostRequest $request)
    {
        Log::error('Lipan store');
        $rules = $this->getValidationRulesForStore($request->guard_name);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Step 2: Create the post
        $post = $this->createPost($request);

        // Step 3: Handle the images
        $this->handlePostImages($request, $post);

        // Step 4: Handle category-specific models
        $this->handleCategorySpecificModels($request, $post);

        // Return a success response
        return response()->json(['message' => 'Post created successfully'], 201);
    }

    private function createPost(Request $request)
    {
        return Post::create([
            'category_id' => Category::getIdByGuardName($request->guard_name),
            'user_id' => auth()->id(),
            'post_time' => now(),
            'title' => $request->adTitle,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => $request->post_type,
            'status' => PostStatus::Pending,
        ]);
    }

    private function handlePostImages(Request $request, Post $post)
    {
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $imageFile) {
                // Store the image
                $path = $imageFile->store($request->guard_name . '/images', 'public');
                // Save image record in the database
                $post->images()->create([
                    'url' => config('app.url') . Storage::url($path)
                ]);
            }
        }
    }

    private function handleCategorySpecificModels(Request $request, Post $post)
    {
        $modelMapping = $this->getModelMapping();

        $modelClass = $modelMapping[$request->guard_name] ?? null;

        if ($modelClass) {
            $request->merge(['post_id' => $post->id]);
            $modelClass::restructureStoreData(array_merge($request->all()));
        }
    }

    private function getModelMapping()
    {
        return [
            CategoryGuardName::Cars->value => PostCar::class,
            CategoryGuardName::HousesApartments->value => PostHousesApartment::class,
            CategoryGuardName::LandPlots->value => PostLandPlot::class,
            CategoryGuardName::Mobiles->value => PostMobile::class,
            CategoryGuardName::Bikes->value => PostBike::class,
            CategoryGuardName::Motorcycles->value => PostBike::class,
            CategoryGuardName::Scooters->value => PostBike::class,
            CategoryGuardName::Bycycles->value => PostBike::class,
            CategoryGuardName::Pets->value => PostPet::class,
            CategoryGuardName::Furniture->value => PostFurniture::class,
            CategoryGuardName::Fashion->value => PostFashion::class,
            CategoryGuardName::ElectronicsAppliances->value => PostElectronicsAppliance::class,
            CategoryGuardName::ShopOffices->value => PostShopOffice::class,
            CategoryGuardName::PgGuestHouses->value => PostPgGuestHouse::class,
            CategoryGuardName::Accessories->value => PostAccessories::class,
            CategoryGuardName::CommercialHeavyVehicles->value => PostHeavyVehicle::class,
            CategoryGuardName::CommercialHeavyMachinery->value => PostHeavyMachinery::class,
            CategoryGuardName::Books->value => PostBook::class,
            CategoryGuardName::SportsInstrument->value => PostSportHobby::class,
            CategoryGuardName::Services->value => PostService::class,
            //Others Post
            CategoryGuardName::Accessories->value => PostOther::class,
            CategoryGuardName::ComputersLaptops->value => PostOther::class,
            CategoryGuardName::TvsVideoAudio->value => PostOther::class,
            CategoryGuardName::Acs->value => PostOther::class,
            CategoryGuardName::Fridges->value => PostOther::class,
            CategoryGuardName::WashingMachines->value => PostOther::class,
            CategoryGuardName::CamerasLenses->value => PostOther::class,
            CategoryGuardName::HarddisksPrintersMonitors->value => PostOther::class,
            CategoryGuardName::KitchenOtherAppliances->value => PostOther::class,
            CategoryGuardName::SofaDining->value => PostOther::class,
            CategoryGuardName::BedsWardrobes->value => PostOther::class,
            CategoryGuardName::HomeDecorGarden->value => PostOther::class,
            CategoryGuardName::KidsFurniture->value => PostOther::class,
            CategoryGuardName::OtherHouseholdItems->value => PostOther::class,
            CategoryGuardName::MensFashion->value => PostOther::class,
            CategoryGuardName::WomensFashion->value => PostOther::class,
            CategoryGuardName::KidsFashion->value => PostOther::class,
            CategoryGuardName::Books->value => PostOther::class,
            CategoryGuardName::GymFitness->value => PostOther::class,
            CategoryGuardName::MusicalInstruments->value => PostOther::class,
            CategoryGuardName::SportsInstrument->value => PostOther::class,
            CategoryGuardName::OtherHobbies->value => PostOther::class,
            CategoryGuardName::Dogs->value => PostOther::class,
            CategoryGuardName::FishAquarium->value => PostOther::class,
            CategoryGuardName::PetsFoodAccessories->value => PostOther::class,
            CategoryGuardName::OtherPets->value => PostOther::class,
            CategoryGuardName::PackersMovers->value => PostService::class,
            CategoryGuardName::OtherServices->value => PostOther::class,
            CategoryGuardName::Others->value => PostOther::class,
            CategoryGuardName::MachinerySpareParts->value => PostOther::class,
            //Job Post
            CategoryGuardName::DataEntryBackOffice->value => PostJob::class,
            CategoryGuardName::SalesMarketing->value => PostJob::class,
            CategoryGuardName::BpoTelecaller->value => PostJob::class,
            CategoryGuardName::Driver->value => PostJob::class,
            CategoryGuardName::OfficeAssistant->value => PostJob::class,
            CategoryGuardName::DeliveryCollection->value => PostJob::class,
            CategoryGuardName::Teacher->value => PostJob::class,
            CategoryGuardName::Cook->value => PostJob::class,
            CategoryGuardName::ReceptionistFrontOffice->value => PostJob::class,
            CategoryGuardName::OperatorTechnician->value => PostJob::class,
            CategoryGuardName::EngineerDeveloper->value => PostJob::class,
            CategoryGuardName::HotelTravelExecutive->value => PostJob::class,
            CategoryGuardName::Accountant->value => PostJob::class,
            CategoryGuardName::Designer->value => PostJob::class,
            CategoryGuardName::OtherJobs->value => PostJob::class,
            //Service Post
            CategoryGuardName::EducationClasses->value => PostService::class,
            CategoryGuardName::ToursTravels->value => PostService::class,
            CategoryGuardName::ElectronicsRepairServices->value => PostService::class,
            CategoryGuardName::HealthBeauty->value => PostService::class,
            CategoryGuardName::HomeRenovationRepair->value => PostService::class,
            CategoryGuardName::CleaningPestControl->value => PostService::class,
            CategoryGuardName::LegalDocumentationSevices->value => PostService::class,
            CategoryGuardName::VehicleSpareParts->value => PostVehicleSpareParts::class,
        ];
    }

    protected function getValidationRulesForStore($guardName)
    {
        switch ($guardName) {
            case CategoryGuardName::Cars->value:
                return (new StorePostCarRequest())->rules();
            case CategoryGuardName::HousesApartments->value:
                return (new StorePostHousesApartmentRequest())->rules();
            case CategoryGuardName::LandPlots->value:
                return (new StorePostLandPlotRequest())->rules();
            case CategoryGuardName::Mobiles->value:
                return (new StorePostMobileRequest())->rules();
                //Start:: Bike
            case CategoryGuardName::Bikes->value:
            case CategoryGuardName::Motorcycles->value:
            case CategoryGuardName::Scooters->value:
            case CategoryGuardName::Bycycles->value:
                return (new StorePostBikeRequest())->rules();
                //Start:: Others Post
            case CategoryGuardName::Accessories->value:
            case CategoryGuardName::ComputersLaptops->value:
            case CategoryGuardName::TvsVideoAudio->value:
            case CategoryGuardName::Acs->value:
            case CategoryGuardName::Fridges->value:
            case CategoryGuardName::WashingMachines->value:
            case CategoryGuardName::CamerasLenses->value:
            case CategoryGuardName::HarddisksPrintersMonitors->value:
            case CategoryGuardName::KitchenOtherAppliances->value:
            case CategoryGuardName::SofaDining->value:
            case CategoryGuardName::BedsWardrobes->value:
            case CategoryGuardName::HomeDecorGarden->value:
            case CategoryGuardName::KidsFurniture->value:
            case CategoryGuardName::OtherHouseholdItems->value:
            case CategoryGuardName::MensFashion->value:
            case CategoryGuardName::WomensFashion->value:
            case CategoryGuardName::KidsFashion->value:
            case CategoryGuardName::Books->value:
            case CategoryGuardName::GymFitness->value:
            case CategoryGuardName::MusicalInstruments->value:
            case CategoryGuardName::SportsInstrument->value:
            case CategoryGuardName::OtherHobbies->value:
            case CategoryGuardName::Dogs->value:
            case CategoryGuardName::FishAquarium->value:
            case CategoryGuardName::PetsFoodAccessories->value:
            case CategoryGuardName::OtherPets->value:
            case CategoryGuardName::PackersMovers->value:
            case CategoryGuardName::OtherServices->value:
            case CategoryGuardName::Others->value:
            case CategoryGuardName::MachinerySpareParts->value:
                return (new StorePostOtherRequest())->rules();
                //End:: Others Post
                //Start:: Job Post
            case CategoryGuardName::DataEntryBackOffice->value:
            case CategoryGuardName::SalesMarketing->value:
            case CategoryGuardName::BpoTelecaller->value:
            case CategoryGuardName::Driver->value:
            case CategoryGuardName::OfficeAssistant->value:
            case CategoryGuardName::DeliveryCollection->value:
            case CategoryGuardName::Teacher->value:
            case CategoryGuardName::Cook->value:
            case CategoryGuardName::ReceptionistFrontOffice->value:
            case CategoryGuardName::OperatorTechnician->value:
            case CategoryGuardName::EngineerDeveloper->value:
            case CategoryGuardName::HotelTravelExecutive->value:
            case CategoryGuardName::Accountant->value:
            case CategoryGuardName::Designer->value:
            case CategoryGuardName::OtherJobs->value:
                return (new StorePostJobRequest())->rules();
                //End:: Job Post
                //Start:: Service Post
            case CategoryGuardName::EducationClasses->value:
            case CategoryGuardName::ToursTravels->value:
            case CategoryGuardName::ElectronicsRepairServices->value:
            case CategoryGuardName::HealthBeauty->value:
            case CategoryGuardName::HomeRenovationRepair->value:
            case CategoryGuardName::CleaningPestControl->value:
            case CategoryGuardName::LegalDocumentationSevices->value:
                return (new StorePostServiceRequest())->rules();
                //End:: Service Post
            case CategoryGuardName::ShopOffices->value:
                return (new StoreShopOfficeRequest())->rules();
            case CategoryGuardName::PgGuestHouses->value:
                return (new StorePgGuestHouseRequest())->rules();
            case CategoryGuardName::CommercialHeavyVehicles->value:
                return (new StorePostHeavyVehicleRequest())->rules();
            case CategoryGuardName::CommercialHeavyMachinery->value:
                return (new StorePostHeavyMachineryRequest())->rules();
            case CategoryGuardName::VehicleSpareParts->value:
                return (new StorePostVehicleSparePartsRequest())->rules();

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
        $userId = auth()->id(); // Get the authenticated user ID

        // Fetch the post along with required relationships
        $posts = Post::with([
            'category',
            'images',
            'user', // Ensure post owner is fetched
            'follower' => function ($query) use ($userId) {
                $query->where('user_id', $userId); // Check if the user follows the post
            }
        ])->where('id', $post->id)->get();

        $posts = ServicesPostService::fetchPostData($posts);

        // Get the post owner
        $postOwnerId = $post->user_id;

        // Check if the logged-in user follows the post owner
        $isFollowingPostUser = auth()->user()?->following()->where('following_id', $postOwnerId)->exists() ?? false;

        // Convert collection to resource and attach `is_following_post_user`
        $postResource = PostResource::collection($posts)[0];
        $postResource->additional(['is_following_post_user' => $isFollowingPostUser]);

        return $postResource;
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
        Log::error('Lipan update');
        if (empty($post)) {
            return response(['status' => 'error', 'message' => 'Could not retrieve data'], 404);
        }
        $rules = $this->getValidationRulesForUpdate($request->guard_name);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Step 2: Create the post
        $post->update([
            'category_id' => Category::getIdByGuardName($request->guard_name),
            'title' => $request->adTitle,
            'address' => $request->address,
            'latitude' => $request->latitude, // Fixed typo here from 'lattitude'
            'longitude' => $request->longitude,
            'type' => $request->post_type,
        ]);

        // Step 3: Handle the images
        $this->handlePostUpdateImages($request, $post);

        // Step 4: Handle category-specific models
        $this->handleCategorySpecificModels($request, $post);

        // Return a success response
        return response()->json(['message' => 'Post updated successfully'], 201);
    }

    private function handlePostUpdateImages(Request $request, Post $post)
    {
        // Handle case when no image parameters are passed
        if (!$request->hasAny(['existing_images', 'new_images', 'deleted_images'])) {
            // Remove all existing images
            $post->images()->each(function ($image) {
                $relativePath = str_replace(config('app.url') . '/storage/', '', $image->url);
                Storage::disk('public')->delete($relativePath);
                $image->delete();
            });
            return;
        }

        // Handle deleted images
        if ($request->has('deleted_images')) {
            $imagesToDelete = $post->images()
                ->whereIn('url', $request->deleted_images)
                ->get();

            foreach ($imagesToDelete as $image) {
                $relativePath = str_replace(config('app.url') . '/storage/', '', $image->url);
                Storage::disk('public')->delete($relativePath);
                $image->delete();
            }
        }

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $imageFile) {
                $path = $imageFile->store($post->guard_name . '/images', 'public');
                $post->images()->create([
                    'url' => config('app.url') . Storage::url($path)
                ]);
            }
        }
    }

    protected function getValidationRulesForUpdate($guardName)
    {
        switch ($guardName) {
            case CategoryGuardName::Cars->value:
                return (new UpdatePostCarRequest())->rules();
            case CategoryGuardName::HousesApartments->value:
                return (new UpdatePostHousesApartmentRequest())->rules();
            case CategoryGuardName::LandPlots->value:
                return (new UpdatePostLandPlotRequest())->rules();
            case CategoryGuardName::Mobiles->value:
                return (new UpdatePostMobileRequest())->rules();
                //Start:: Bike
            case CategoryGuardName::Bikes->value:
            case CategoryGuardName::Motorcycles->value:
            case CategoryGuardName::Scooters->value:
            case CategoryGuardName::Bycycles->value:
                return (new UpdatePostBikeRequest())->rules();
                //Start:: Others Post
            case CategoryGuardName::Accessories->value:
            case CategoryGuardName::ComputersLaptops->value:
            case CategoryGuardName::TvsVideoAudio->value:
            case CategoryGuardName::Acs->value:
            case CategoryGuardName::Fridges->value:
            case CategoryGuardName::WashingMachines->value:
            case CategoryGuardName::CamerasLenses->value:
            case CategoryGuardName::HarddisksPrintersMonitors->value:
            case CategoryGuardName::KitchenOtherAppliances->value:
            case CategoryGuardName::SofaDining->value:
            case CategoryGuardName::BedsWardrobes->value:
            case CategoryGuardName::HomeDecorGarden->value:
            case CategoryGuardName::KidsFurniture->value:
            case CategoryGuardName::OtherHouseholdItems->value:
            case CategoryGuardName::MensFashion->value:
            case CategoryGuardName::WomensFashion->value:
            case CategoryGuardName::KidsFashion->value:
            case CategoryGuardName::Books->value:
            case CategoryGuardName::GymFitness->value:
            case CategoryGuardName::MusicalInstruments->value:
            case CategoryGuardName::SportsInstrument->value:
            case CategoryGuardName::OtherHobbies->value:
            case CategoryGuardName::Dogs->value:
            case CategoryGuardName::FishAquarium->value:
            case CategoryGuardName::PetsFoodAccessories->value:
            case CategoryGuardName::OtherPets->value:
            case CategoryGuardName::PackersMovers->value:
            case CategoryGuardName::OtherServices->value:
            case CategoryGuardName::Others->value:
            case CategoryGuardName::MachinerySpareParts->value:
                return (new UpdatePostOtherRequest())->rules();
                //End:: Others Post
                //Start:: Job Post
            case CategoryGuardName::DataEntryBackOffice->value:
            case CategoryGuardName::SalesMarketing->value:
            case CategoryGuardName::BpoTelecaller->value:
            case CategoryGuardName::Driver->value:
            case CategoryGuardName::OfficeAssistant->value:
            case CategoryGuardName::DeliveryCollection->value:
            case CategoryGuardName::Teacher->value:
            case CategoryGuardName::Cook->value:
            case CategoryGuardName::ReceptionistFrontOffice->value:
            case CategoryGuardName::OperatorTechnician->value:
            case CategoryGuardName::EngineerDeveloper->value:
            case CategoryGuardName::HotelTravelExecutive->value:
            case CategoryGuardName::Accountant->value:
            case CategoryGuardName::Designer->value:
            case CategoryGuardName::OtherJobs->value:
                return (new UpdatePostJobRequest())->rules();
                //End:: Job Post
                //Start:: Service Post
            case CategoryGuardName::EducationClasses->value:
            case CategoryGuardName::ToursTravels->value:
            case CategoryGuardName::ElectronicsRepairServices->value:
            case CategoryGuardName::HealthBeauty->value:
            case CategoryGuardName::HomeRenovationRepair->value:
            case CategoryGuardName::CleaningPestControl->value:
            case CategoryGuardName::LegalDocumentationSevices->value:
                return (new UpdatePostServiceRequest())->rules();
                //End:: Service Post
            case CategoryGuardName::ShopOffices->value:
                return (new UpdateShopOfficeRequest())->rules();
            case CategoryGuardName::PgGuestHouses->value:
                return (new UpdatePgGuestHouseRequest())->rules();
            case CategoryGuardName::CommercialHeavyVehicles->value:
                return (new UpdatePostHeavyVehicleRequest())->rules();
            case CategoryGuardName::CommercialHeavyMachinery->value:
                return (new UpdatePostHeavyMachineryRequest())->rules();
            case CategoryGuardName::VehicleSpareParts->value:
                return (new UpdatePostVehicleSparePartsRequest())->rules();
            default:
                return [
                    'guard_name' => ['required', 'string', Rule::in(CategoryGuardName::allTypes())],
                    'post_type' => ['required', 'string', Rule::in(PostType::allTypes())],
                ];
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Delete associated images first
        $images = $post->images;

        foreach ($images as $image) {
            // Remove image from storage
            $relativePath = str_replace(config('app.url') . '/storage/', '', $image->url);
            Storage::disk('public')->delete($relativePath);

            // Delete image record
            $image->delete();
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
