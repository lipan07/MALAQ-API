<?php

namespace App\Enums;

enum CategoryGuardName: string
{
    case Cars = 'cars';
    case Properties = 'properties';
    case HousesApartments = 'houses_apartments';
    case LandPlots = 'land_plots';
    case PgGuestHouses = 'pg_guest_houses';
    case ShopOffices = 'shop_offices';
    case Mobiles = 'mobiles';
    case Job = 'job';
    case DataEntryBackOffice = 'data_entry_back_office';
    case SalesMarketing = 'sales_marketing';
    case BpoTelecaller = 'bpo_telecaller';
    case Driver = 'driver';
    case OfficeAssistant = 'office_assistant';
    case DeliveryCollection = 'delivery_collection';
    case Teacher = 'teacher';
    case Cook = 'cook';
    case ReceptionistFrontOffice = 'receptionist_front_office';
    case OperatorTechnician = 'operator_technician';
    case EngineerDeveloper = 'engineer_developer';
    case HotelTravelExecutive = 'hotel_travel_executive';
    case Accountant = 'accountant';
    case Designer = 'designer';
    case OtherJobs = 'other_jobs';
    case Bikes = 'bikes';
    case Motorcycles = 'motorcycles';
    case Scooters = 'scooters';
    case Bycycles = 'bycycles';
    case Accessories = 'accessories';
    case ElectronicsAppliances = 'electronics_appliances';
    case ComputersLaptops = 'computers_laptops';
    case TvsVideoAudio = 'tvs_video_audio';
    case Acs = 'acs';
    case Fridges = 'fridges';
    case WashingMachines = 'washing_machines';
    case CamerasLenses = 'cameras_lenses';
    case HarddisksPrintersMonitors = 'harddisks_printers_monitors';
    case KitchenOtherAppliances = 'kitchen_other_appliances';
    case CommercialVehicleSparePart = 'commercial_vehicle_spare_part';
    case CommercialHeavyVehicles = 'commercial_heavy_vehicles';
    case VehicleSpareParts = 'vehicle_spare_parts';
    case CommercialMachinerySpareParts = 'commercial_machinery_spare_parts';
    case CommercialHeavyMachinery = 'commercial_heavy_machinery';
    case MachinerySpareParts = 'machinery_spare_parts';
    case Furniture = 'furniture';
    case SofaDining = 'sofa_dining';
    case BedsWardrobes = 'beds_wardrobes';
    case HomeDecorGarden = 'home_decor_garden';
    case KidsFurniture = 'kids_furniture';
    case OtherHouseholdItems = 'other_household_items';
    case Fashion = 'fashion';
    case MensFashion = 'mens_fashion';
    case WomensFashion = 'womens_fashion';
    case KidsFashion = 'kids_fashion';
    case BooksSportsHobbies = 'books_sports_hobbies';
    case Books = 'books';
    case GymFitness = 'gym_fitness';
    case MusicalInstruments = 'musical_instruments';
    case SportsInstrument = 'sports_instrument';
    case OtherHobbies = 'other_hobbies';
    case Pets = 'pets';
    case Dogs = 'dogs';
    case FishAquarium = 'fish_aquarium';
    case PetsFoodAccessories = 'pets_food_accessories';
    case OtherPets = 'other_pets';
    case Services = 'services';
    case EducationClasses = 'education_classes';
    case ToursTravels = 'tours_travels';
    case ElectronicsRepairServices = 'electronics_repair_services';
    case HealthBeauty = 'health_beauty';
    case HomeRenovationRepair = 'home_renovation_repair';
    case CleaningPestControl = 'cleaning_pest_control';
    case LegalDocumentationSevices = 'legal_documentation_sevices';
    case PackersMovers = 'packers_movers';
    case OtherServices = 'other_services';
    case Others = 'others';

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
