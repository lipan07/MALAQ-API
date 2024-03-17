<?php

namespace App\Models;

use App\Enums\CarFuelType;
use App\Enums\CommercialVehicleBrand;
use App\Enums\Condition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostHeavyVehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'title',
        'brand',
        'model',
        'year',
        'condition',
        'km_driven',
        'fuel_type',
        'price',
        'description',
        'contact_name',
        'contact_phone',
    ];

    protected $casts = [
        'brand' => CommercialVehicleBrand::class,
        'condition' => Condition::class,
        'fuel_type' => CarFuelType::class,
    ];
}
