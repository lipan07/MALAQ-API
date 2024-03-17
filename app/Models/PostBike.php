<?php

namespace App\Models;

use App\Enums\VehicleBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostBike extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'brand',
        'year',
        'km_driven',
        'title',
        'description',
        'amount',
    ];

    protected $casts = [
        'brand' => VehicleBrand::class,
    ];
}
