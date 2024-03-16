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
}
