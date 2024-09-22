<?php

namespace App\Models;

use App\Enums\PropertyConstructionStatus;
use App\Enums\PropertyFacing;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostHousesApartment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'property_type',
        'bedrooms',
        'bathrooms',
        'furnishing',
        'construction_status',
        'listed_by',
        'super_builtup_area',
        'carpet_area',
        'monthly_maintenance',
        'total_floors',
        'floor_no',
        'car_parking',
        'facing',
        'project_name',
        'title',
        'description',
        'amount',
    ];

    protected $casts = [
        'furnishing' => PropertyFurnishing::class,
        'construction_status' => PropertyConstructionStatus::class,
        'listed_by' => PropertyListedBy::class,
        'facing' => PropertyFacing::class,
        'property_type' => PropertyType::class,
        'facing' => PropertyFacing::class
    ];

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'post_id' => $data['post_id'] ?? null,
            'property_type' => $data['propertyType'] ?? null,
            'bedrooms' => $data['bedroom'] ?? null,
            'bathrooms' => $data['bathroom'] ?? null,
            'furnishing' => $data['furnishing'] ?? null,
            'construction_status' => $data['constructionStatus'] ?? null,
            'listed_by' => $data['listedBy'] ?? null,
            'super_builtup_area' => $data['superBuiltupArea'] ?? null,
            'carpet_area' => $data['carpetArea'] ?? null,
            'monthly_maintenance' => $data['maintenance'] ?? null,
            'total_floors' => $data['totalFloors'] ?? null,
            'floor_no' => $data['floorNo'] ?? null,
            'car_parking' => $data['carParking'] ?? null,
            'facing' => $data['facing'] ?? null,
            'project_name' => $data['projectName'] ?? null,
            'amount' => $data['amount'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::create($restructuredData);
    }
}
