<?php

namespace App\Models;

use App\Enums\CarBrand;
use App\Enums\CarFuelType;
use App\Enums\CarNoOfOwner;
use App\Enums\CarTransmission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostCar extends Model
{
    use HasFactory, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'brand',
        'year',
        'fuel',
        'transmission',
        'km_driven',
        'no_of_owner',
        'title',
        'description',
        'amount'
    ];
    protected $casts = [
        'brand' => CarBrand::class,
        'fuel' => CarFuelType::class,
        'transmission' => CarTransmission::class,
        'no_of_owner' => CarNoOfOwner::class,
    ];
}
