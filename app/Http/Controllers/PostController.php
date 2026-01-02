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
use App\Models\User;
use App\Services\PostService as ServicesPostService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;

class PostController extends Controller
{
    private array $jobGuardNames = [
        CategoryGuardName::DataEntryBackOffice->value,
        CategoryGuardName::SalesMarketing->value,
        CategoryGuardName::BpoTelecaller->value,
        CategoryGuardName::Driver->value,
        CategoryGuardName::OfficeAssistant->value,
        CategoryGuardName::DeliveryCollection->value,
        CategoryGuardName::Teacher->value,
        CategoryGuardName::Cook->value,
        CategoryGuardName::ReceptionistFrontOffice->value,
        CategoryGuardName::OperatorTechnician->value,
        CategoryGuardName::EngineerDeveloper->value,
        CategoryGuardName::HotelTravelExecutive->value,
        CategoryGuardName::Accountant->value,
        CategoryGuardName::Designer->value,
        CategoryGuardName::OtherJobs->value,
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(FetchPostsRequest $request)
    {
        $userId = Auth::id();
        $finalPosts = null;

        // Check cache first
        $cacheKey = $request->all();
        $cachedPosts = CacheService::getCachedPosts($cacheKey);

        if ($cachedPosts) {
            return $cachedPosts;
        }

        // Only attempt location-based search if coordinates are provided
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $requestedDistance = $request->distance ?? 5;

            $postsQuery = Post::query();

            // Apply category filter if provided
            if ($request->filled('category')) {
                if (!in_array($request->category, [1, 7, 76])) {
                    $hasSubCategories = Category::where('parent_id', $request->category)->exists();

                    if (!$hasSubCategories) {
                        return response()->json([
                            'message' => 'This category does not have any subcategories.',
                            'sub_category_ids' => [],
                        ], 404);
                    }

                    $subCategoryIds = Category::where('parent_id', $request->category)->pluck('id')->toArray();
                    $postsQuery->whereIn('category_id', $subCategoryIds);
                } else {
                    $postsQuery->where('category_id', $request->category);
                }
            }

            // Apply search term filter if provided
            if ($request->filled('search')) {
                $postsQuery->where('title', 'LIKE', '%' . $request->search . '%');
            }

            // Apply listing type filter if provided
            if ($request->filled('listingType')) {
                $postsQuery->where('type', $request->listingType ?? PostType::defaultType()->value);
            }

            // Apply price range filter if provided
            if ($request->filled('priceRange') && is_array($request->priceRange) && count($request->priceRange) >= 2) {
                $minPrice = $request->priceRange[0];
                $maxPrice = $request->priceRange[1];

                // Only apply filters if values are provided and valid
                if (!empty($minPrice) && is_numeric($minPrice)) {
                    $postsQuery->where('amount', '>=', (float)$minPrice);
                }

                if (!empty($maxPrice) && is_numeric($maxPrice)) {
                    $postsQuery->where('amount', '<=', (float)$maxPrice);
                }
            }

            // Apply location filter with current distance tier
            $postsQuery->selectRaw(
                "*, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )->having('distance', '<=', $requestedDistance);

            if ($request->filled('sortBy')) {
                $sortBy = $request->sortBy;
                switch ($sortBy) {
                    case 'Recently Added':
                    case 'createdAt_desc':
                        $postsQuery->orderByDesc('post_time');
                        break;
                    case 'Price: Low to High':
                    case 'price_asc':
                        $postsQuery->orderByRaw('CAST(amount AS DECIMAL(15,2)) asc');
                        break;
                    case 'Price: High to Low':
                    case 'price_desc':
                        $postsQuery->orderByRaw('CAST(amount AS DECIMAL(15,2)) desc');
                        break;
                    case 'Relevance':
                    default:
                        // Default relevance for location-based: nearest first
                        // $postsQuery->orderBy('distance'); // put this line if you want to use location-based search
                        $postsQuery->orderByDesc('post_time'); // remove this line if you want to use location-based search
                        break;
                }
                $postsQuery->where('amount', '>', 0);
            } else {
                // No sort provided: nearest first
                // $postsQuery->orderBy('distance'); // put this line if you want to use location-based search
                $postsQuery->orderByDesc('post_time'); // remove this line if you want to use location-based search
            }

            $perPage = (int) ($request->input('limit', 15));
            $posts = $postsQuery->where('status', PostStatus::Active)->simplePaginate($perPage);

            // If we found posts, break out of the loop
            if ($posts->count() > 0) {
                $finalPosts = $posts;
            }
        } else if ($request->filled('user_id')) {
            $postsQuery = Post::query();
            $postsQuery->where('user_id', $request->user_id);
            $perPage = (int) ($request->input('limit', 15));
            $posts = $postsQuery->where('status', PostStatus::Active)->simplePaginate($perPage);

            // If we found posts, break out of the loop
            if ($posts->count() > 0) {
                $finalPosts = $posts;
            }
        } else {
            // Generic filter (no location provided)
            $postsQuery = Post::query();

            // Category filter
            if ($request->filled('category')) {
                if (!in_array($request->category, [1, 7, 76])) {
                    $hasSubCategories = Category::where('parent_id', $request->category)->exists();
                    if ($hasSubCategories) {
                        $subCategoryIds = Category::where('parent_id', $request->category)->pluck('id')->toArray();
                        $postsQuery->whereIn('category_id', $subCategoryIds);
                    } else {
                        $postsQuery->where('category_id', $request->category);
                    }
                } else {
                    $postsQuery->where('category_id', $request->category);
                }
            }

            // Search
            if ($request->filled('search')) {
                $postsQuery->where('title', 'LIKE', '%' . $request->search . '%');
            }

            // Listing type
            if ($request->filled('listingType')) {
                $postsQuery->where('type', $request->listingType ?? PostType::defaultType()->value);
            }

            // Price range
            if ($request->filled('priceRange') && is_array($request->priceRange) && count($request->priceRange) >= 2) {
                $minPrice = $request->priceRange[0];
                $maxPrice = $request->priceRange[1];
                if (!empty($minPrice) && is_numeric($minPrice)) {
                    $postsQuery->where('amount', '>=', (float)$minPrice);
                }
                if (!empty($maxPrice) && is_numeric($maxPrice)) {
                    $postsQuery->where('amount', '<=', (float)$maxPrice);
                }
            }

            // Sorting
            if ($request->filled('sortBy')) {
                $sortBy = $request->sortBy;
                switch ($sortBy) {
                    case 'Recently Added':
                    case 'createdAt_desc':
                        $postsQuery->orderByDesc('post_time');
                        break;
                    case 'Price: Low to High':
                    case 'price_asc':
                        $postsQuery->orderByRaw('CAST(amount AS DECIMAL(15,2)) asc');
                        break;
                    case 'Price: High to Low':
                    case 'price_desc':
                        $postsQuery->orderByRaw('CAST(amount AS DECIMAL(15,2)) desc');
                        break;
                    case 'Relevance':
                    default:
                        // Default by latest
                        $postsQuery->orderByDesc('post_time');
                        break;
                }
            } else {
                $postsQuery->orderByDesc('post_time');
            }

            // $posts = $postsQuery->where('status', PostStatus::Active)->simplePaginate(15);
            $posts = $postsQuery->simplePaginate(15);
            if ($posts->count() > 0) {
                $finalPosts = $posts;
            }
        }

        if (!$finalPosts) {
            // Return an empty collection or a suitable response
            return PostResource::collection(collect());
        }

        // Optimized eager loading with selective fields
        $finalPosts->load([
            'user:id,name,status,last_activity',
            'category:id,name',
        ]);

        // Check if it's page 1 and user is logged in, then merge pending posts at the top
        $currentPage = $request->input('page', 1);
        if ($currentPage == 1 && $userId) {
            // Fetch logged-in user's pending posts
            $pendingPostsQuery = Post::where('user_id', $userId)
                ->where('status', PostStatus::Pending)
                ->orderByDesc('post_time');

            $pendingPosts = $pendingPostsQuery->get();

            if ($pendingPosts->count() > 0) {
                // Eager load relationships for pending posts
                $pendingPosts->load([
                    'user:id,name,status,last_activity',
                    'category:id,name',
                ]);

                // Get current active posts items
                $activePostsItems = collect($finalPosts->items());

                // Merge pending posts at the top
                $mergedItems = $pendingPosts->merge($activePostsItems);

                // Create a new paginator with merged items
                $perPage = $finalPosts->perPage();
                $currentPath = $request->url();
                $queryParams = $request->query();

                $finalPosts = new Paginator(
                    $mergedItems,
                    $perPage,
                    $currentPage,
                    [
                        'path' => $currentPath,
                        'pageName' => 'page',
                    ]
                );

                // Set query parameters for pagination links
                $finalPosts->appends($queryParams);
            }
        }

        // Fetch additional data for posts with optimized queries
        $finalPosts = ServicesPostService::fetchPostData($finalPosts);

        // Set list mode flag for performance (skip signed URL generation)
        PostResource::$isListMode = true;
        $response = PostResource::collection($finalPosts);
        PostResource::$isListMode = false; // Reset after collection

        // Cache the response
        CacheService::cachePosts($cacheKey, $response);

        return $response;
    }

    public function myPost()
    {
        $user = auth()->user();
        $posts = Post::where(['user_id' => $user->id])->orderBy('created_at', 'DESC')->simplePaginate(10);
        $posts->load([
            'user',
            'category',
        ]);

        $posts = ServicesPostService::fetchPostData($posts);
        // Return the restructured paginated result

        // Set list mode flag for performance (skip signed URL generation)
        PostResource::$isListMode = true;
        $response = PostResource::collection($posts);
        PostResource::$isListMode = false; // Reset after collection
        return $response;
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
        $rules = $this->getValidationRulesForStore($request->guard_name);

        // Make amount optional for donate and requirement posts
        if ($request->listingType === 'donate' || $request->listingType === 'post_requirement') {
            $rules['amount'] = 'nullable|numeric|min:0';
        } else {
            // For non-donate/requirement posts, add amount validation if not already present
            if (!isset($rules['amount'])) {
                $rules['amount'] = 'nullable|numeric|min:0';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Job posts: use single amount directly instead of range

        // Step 2: Create the post
        $post = $this->createPost($request);

        // Step 3: Handle the images
        $this->handlePostImages($request, $post);

        // Step 3.5: Handle the videos
        $this->handlePostVideos($request, $post);

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
            'amount' => ($request->listingType === 'donate' || $request->listingType === 'post_requirement') ? null : $request->amount,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => $request->listingType,
            'status' => PostStatus::Pending,
            'show_phone' => $this->convertToBoolean($request->show_phone),
        ]);
    }

    private function handlePostImages(Request $request, Post $post)
    {
        if ($request->hasFile('new_images')) {
            $imageUrls = $post->images ?? [];
            foreach ($request->file('new_images') as $imageFile) {
                // Store the image
                $path = $imageFile->store($request->guard_name . '/images', 'public');
                // Add image URL to the array
                $imageUrls[] = config('app.url') . Storage::url($path);
            }
            // Update post with new images array
            $post->update(['images' => $imageUrls]);
        }
    }

    private function handlePostVideos(Request $request, Post $post)
    {
        try {
            // Handle deleted video - delete from Backblaze
            if ($request->has('deleted_video_url') || $request->has('deleted_video_id')) {
                $deletedVideoUrl = $request->input('deleted_video_url');
                $deletedVideoId = $request->input('deleted_video_id');

                try {
                    // Call Backblaze service to delete video
                    $backblazeService = app(\App\Services\BackblazeService::class);
                    if ($deletedVideoId) {
                        $backblazeService->deleteVideo($deletedVideoId);
                    } elseif ($deletedVideoUrl) {
                        $backblazeService->deleteVideoByUrl($deletedVideoUrl);
                    }
                    \Log::info('Video deleted from Backblaze', [
                        'post_id' => $post->id,
                        'video_id' => $deletedVideoId,
                        'video_url' => $deletedVideoUrl,
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete video from Backblaze', [
                        'post_id' => $post->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue even if Backblaze deletion fails
                }
            }

            // Handle new video URL
            if ($request->has('videoUrl')) {
                $videoUrl = $request->input('videoUrl');
                
                if (!empty($videoUrl) && filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                    // Store video URL in posts table
                    $post->update(['videos' => [$videoUrl]]);
                } elseif (empty($videoUrl) || $videoUrl === null) {
                    // If videoUrl is explicitly empty/null, remove all videos
                    $post->update(['videos' => null]);
                }
            } elseif ($request->has('deleted_video_url') || $request->has('deleted_video_id')) {
                // If video was deleted but no new video provided, remove videos
                $post->update(['videos' => null]);
            }
        } catch (\Exception $e) {
            \Log::error('Error handling post videos: ' . $e->getMessage(), [
                'post_id' => $post->id,
                'video_url' => $request->input('videoUrl'),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - allow post creation/update to continue even if video handling fails
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
                    'listingType' => ['nullable', 'string', Rule::in(PostType::allTypes())],
                ];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $userId = auth()->id(); // Get the authenticated user ID

        // Track view if user is not the post owner
        if ($userId && $userId !== $post->user_id) {
            $this->trackPostView($post->id, $userId);
        }

        // Fetch the post along with required relationships
        $posts = Post::with([
            'category',
            'user', // Ensure post owner is fetched
        ])->where('id', $post->id)->get();

        $posts = ServicesPostService::fetchPostData($posts);

        // Get the post owner
        $postOwnerId = $post->user_id;

        // Check if the logged-in user follows the post owner
        $isFollowingPostUser = auth()->user()?->following()->where('following_id', $postOwnerId)->exists() ?? false;

        // Check if user has liked the post
        $isLiked = \App\Models\PostLike::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->exists();

        // Convert collection to resource and attach additional data
        // Don't set list mode for show method - we want full video URLs with signed URLs
        PostResource::$isListMode = false;
        $postResource = PostResource::collection($posts)[0];
        $postResource->additional([
            'is_following_post_user' => $isFollowingPostUser,
            'is_liked' => $isLiked,
            'is_post_owner' => $userId === $postOwnerId
        ]);

        return $postResource;
    }

    /**
     * Convert various representations to boolean
     */
    private function convertToBoolean($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }

        if (is_numeric($value)) {
            return (int)$value === 1;
        }

        return false;
    }

    /**
     * Track a post view
     */
    private function trackPostView($postId, $userId)
    {
        try {
            // Check if user has already viewed this post
            $existingView = \App\Models\PostView::where('user_id', $userId)
                ->where('post_id', $postId)
                ->first();

            if (!$existingView) {
                // Create new view record
                \App\Models\PostView::create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'viewed_at' => now(),
                ]);

                // Increment view count
                Post::where('id', $postId)->increment('view_count');
            }
        } catch (\Exception $e) {
            \Log::error('Error tracking post view: ' . $e->getMessage());
        }
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
        if (empty($post)) {
            return response(['status' => 'error', 'message' => 'Could not retrieve data'], 404);
        }
        $rules = $this->getValidationRulesForUpdate($request->guard_name);

        // Make amount optional for donate and requirement posts
        if ($request->listingType === 'donate' || $request->listingType === 'post_requirement' || 
            $post->type === 'donate' || $post->type === 'post_requirement') {
            $rules['amount'] = 'nullable|numeric|min:0';
        } else {
            // For non-donate/requirement posts, add amount validation if not already present
            if (!isset($rules['amount'])) {
                $rules['amount'] = 'nullable|numeric|min:0';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Job posts: use single amount directly instead of range

        // Step 2: Create the post
        $post->update([
            'category_id' => Category::getIdByGuardName($request->guard_name),
            'title' => $request->adTitle,
            'address' => $request->address,
            'amount' => ($request->listingType === 'donate' || $request->listingType === 'post_requirement' || 
                         $post->type === 'donate' || $post->type === 'post_requirement') ? null : $request->amount,
            'latitude' => $request->latitude, // Fixed typo here from 'lattitude'
            'longitude' => $request->longitude,
            'type' => $request->listingType,
            'status' => PostStatus::Pending,
            'show_phone' => $this->convertToBoolean($request->show_phone),
        ]);

        // Step 3: Handle the images
        $this->handlePostUpdateImages($request, $post);

        // Step 3.5: Handle the videos
        $this->handlePostVideos($request, $post);

        // Step 4: Handle category-specific models
        $this->handleCategorySpecificModels($request, $post);

        // Return a success response
        return response()->json(['message' => 'Post updated successfully'], 201);
    }

    private function handlePostUpdateImages(Request $request, Post $post)
    {
        // Handle case when no image parameters are passed
        if (!$request->hasAny(['existing_images', 'new_images', 'deleted_images'])) {
            // Delete all image files from storage
            $currentImages = $post->images ?? [];
            foreach ($currentImages as $imageUrl) {
                $relativePath = str_replace(config('app.url') . '/storage/', '', $imageUrl);
                Storage::disk('public')->delete($relativePath);
            }
            // Remove all images from posts table
            $post->update(['images' => null]);
            return;
        }

        // Start with existing images if provided, otherwise use current images
        $currentImages = $request->has('existing_images') 
            ? $request->existing_images 
            : ($post->images ?? []);

        // Handle deleted images
        if ($request->has('deleted_images')) {
            $deletedUrls = $request->deleted_images;
            foreach ($deletedUrls as $imageUrl) {
                // Delete file from storage
                $relativePath = str_replace(config('app.url') . '/storage/', '', $imageUrl);
                Storage::disk('public')->delete($relativePath);
            }
            // Remove deleted images from array
            $currentImages = array_values(array_diff($currentImages, $deletedUrls));
        }

        // Handle new images
        if ($request->hasFile('new_images')) {
            $guardName = $request->guard_name ?? Category::getGuardNameById($post->category_id) ?? 'default';
            foreach ($request->file('new_images') as $imageFile) {
                $path = $imageFile->store($guardName . '/images', 'public');
                $currentImages[] = config('app.url') . Storage::url($path);
            }
        }

        // Update post with final images array
        $post->update(['images' => !empty($currentImages) ? $currentImages : null]);
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
                    'listingType' => ['required', 'string', Rule::in(PostType::allTypes())],
                ];
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }


    /**
     * Display a listing of the resource.
     */
    public function sellersPost(Request $request, User $user)
    {
        $posts = Post::where('status', PostStatus::Active)->where('user_id', $user->id)->simplePaginate(15);

        // Eager load relationships
        $posts->load([
            'user',
            'category',
        ]);

        // Fetch additional data for posts
        $finalPosts = ServicesPostService::fetchPostData($posts);

        // Set list mode flag for performance (skip signed URL generation)
        PostResource::$isListMode = true;
        $response = PostResource::collection($finalPosts);
        PostResource::$isListMode = false; // Reset after collection
        return $response;
    }
}
