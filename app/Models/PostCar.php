<?php

namespace App\Models;

use App\Enums\CarBrand;
use App\Enums\CarFuelType;
use App\Enums\CarNoOfOwner;
use App\Enums\CarTransmission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCar extends Model
{
    use HasFactory;

    protected $casts = [
        'brand' => CarBrand::class,
        'fuel' => CarFuelType::class,
        'transmission' => CarTransmission::class,
        'no_of_owner' => CarNoOfOwner::class,
    ];
}
