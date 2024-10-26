<?php

namespace App\Services;

use App\Enums\CategoryGuardName;
use App\Models\Category;

class PostService
{
    public static function fetchPostData($posts)
    {
        foreach ($posts as $post) {
            self::loadCategoryRelationship($post);
        }
        return $posts;
    }

    public static function fetchSinglePostData($post)
    {
        self::loadCategoryRelationship($post);
        return $post;
    }

    private static function loadCategoryRelationship($post)
    {
        $categoryGuardName = Category::getGuardNameById($post->category_id);

        switch ($categoryGuardName) {
            case CategoryGuardName::Mobiles->value:
                $post->load('mobile');
                break;
            case CategoryGuardName::Cars->value:
                $post->load('car');
                break;
            case CategoryGuardName::HousesApartments->value:
                $post->load('housesApartment');
                break;
            case CategoryGuardName::LandPlots->value:
                $post->load('landPlots');
                break;
            case CategoryGuardName::Fashion->value:
                $post->load('fashion');
                break;
            case CategoryGuardName::Bikes->value:
                $post->load('bikes');
                break;
            case CategoryGuardName::Job->value:
                $post->load('jobs');
                break;
            case CategoryGuardName::Pets->value:
                $post->load('pets');
                break;
            case CategoryGuardName::Furniture->value:
                $post->load('furnitures');
                break;
            case CategoryGuardName::ElectronicsAppliances->value:
                $post->load('electronicsAppliances');
                break;
            case CategoryGuardName::Others->value:
                $post->load('others');
                break;
            case CategoryGuardName::ShopOffices->value:
                $post->load('shopOffices');
                break;
            case CategoryGuardName::PgGuestHouses->value:
                $post->load('pgGuestHouses');
                break;
            case CategoryGuardName::Accessories->value:
                $post->load('accessories');
                break;
            case CategoryGuardName::CommercialHeavyVehicles->value:
                $post->load('commercialHeavyVehicles');
                break;
            case CategoryGuardName::CommercialHeavyMachinery->value:
                $post->load('commercialHeavyMachinery');
                break;
            case CategoryGuardName::Books->value:
                $post->load('books');
                break;
            case CategoryGuardName::SportsInstrument->value:
                $post->load('sportsInstruments');
                break;
            case CategoryGuardName::Services->value:
                $post->load('services');
                break;
            case CategoryGuardName::VehicleSpareParts->value:
                $post->load('vehicleSpareParts');
                break;
                // Add other cases here...
        }
    }
}
