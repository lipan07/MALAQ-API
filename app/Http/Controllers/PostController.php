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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posts = Post::with('category', 'images');
        if ($request->category) {
            $posts->where('category_id', $request->category);
        }
        $posts = $posts->orderBy('created_at', 'DESC')->paginate(15);

        foreach ($posts as $post) {
            $categoryGuardName = Category::getGuardNameById($post->category_id);
            switch ($categoryGuardName) {
                case 'mobiles':
                    $post->load('mobile');
                    break;
                case 'cars':
                    $post->load('car');
                    break;
                case 'houses_apartments':
                    $post->load('housesApartment');
                    break;
                case 'land_plots':
                    $post->load('landPlots');
                    break;
                case 'fashion':
                    $post->load('fashion');
                    break;
                case 'bikes':
                    $post->load('bikes');
                    break;
                case 'job':
                    $post->load('jobs');
                    break;
                case 'pets':
                    $post->load('pets');
                    break;
                case 'furniture':
                    $post->load('furnitures');
                    break;
                case 'electronics_appliances':
                    $post->load('electronicsAppliances');
                    break;
                case 'others':
                    $post->load('others');
                    break;
                case 'shop_offices':
                    $post->load('shopOffices');
                    break;
                case 'pg_guest_houses':
                    $post->load('pgGuestHouses');
                    break;
                case 'accessories':
                    $post->load('accessories');
                    break;
                case 'commercial_heavy_vehicles':
                    $post->load('commercialHeavyVehicles');
                    break;
                case 'commercial_heavy_machinery':
                    $post->load('commercialHeavyMachinery');
                    break;
                case 'books':
                    $post->load('books');
                    break;
                case 'sports_instrument':
                    $post->load('sportsInstruments');
                    break;
                case 'services':
                    $post->load('services');
                    break;

                    // Add more cases for other categories if needed
            }
        }

        // return PostResource::collection($posts);

        return response()->json([
            'status' => 'success',
            'data' => PostResource::collection($posts), // Get paginated data
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'first_page_url' => $posts->url(1),
                'last_page_url' => $posts->url($posts->lastPage()),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ]
        ]);
    }


    public function myPost()
    {
        $user = auth()->user();
        $posts = Post::with('category', 'images')->where(['user_id' => $user->id])->orderBy('created_at', 'DESC')->simplePaginate(6);

        foreach ($posts as $post) {
            $categoryGuardName = Category::getGuardNameById($post->category_id);
            switch ($categoryGuardName) {
                case 'mobiles':
                    $post->load('mobile');
                    break;
                case 'cars':
                    $post->load('car');
                    break;
                case 'houses_apartments':
                    $post->load('housesApartment');
                    break;
                case 'land_plots':
                    $post->load('landPlots');
                    break;
                case 'fashion':
                    $post->load('fashion');
                    break;
                case 'bikes':
                    $post->load('bikes');
                    break;
                case 'job':
                    $post->load('jobs');
                    break;
                case 'pets':
                    $post->load('pets');
                    break;
                case 'furniture':
                    $post->load('furnitures');
                    break;
                case 'electronics_appliances':
                    $post->load('electronicsAppliances');
                    break;
                case 'others':
                    $post->load('others');
                    break;
                case 'shop_offices':
                    $post->load('shopOffices');
                    break;
                case 'pg_guest_houses':
                    $post->load('pgGuestHouses');
                    break;
                case 'accessories':
                    $post->load('accessories');
                    break;
                case 'commercial_heavy_vehicles':
                    $post->load('commercialHeavyVehicles');
                    break;
                case 'commercial_heavy_machinery':
                    $post->load('commercialHeavyMachinery');
                    break;
                case 'books':
                    $post->load('books');
                    break;
                case 'sports_instrument':
                    $post->load('sportsInstruments');
                    break;
                case 'services':
                    $post->load('services');
                    break;

                    // Add more cases for other categories if needed
            }
        }
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
            'address' => $request->address,
            'latitude' => $request->latitude, // Fixed typo here from 'lattitude'
            'longitude' => $request->longitude,
            'type' => $request->post_type,
            'status' => PostStatus::Pending,
        ]);
    }

    private function handlePostImages(Request $request, Post $post)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
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
            'address' => $request->address,
            'latitude' => $request->latitude, // Fixed typo here from 'lattitude'
            'longitude' => $request->longitude,
            'type' => $request->post_type,
        ]);

        // Step 3: Handle the images
        $this->handlePostImages($request, $post);

        // Step 4: Handle category-specific models
        $this->handleCategorySpecificModels($request, $post);

        // Return a success response
        return response()->json(['message' => 'Post updated successfully'], 201);
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
        //
    }
}
