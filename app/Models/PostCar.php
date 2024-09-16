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
    protected $hidden = ['post_id'];

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'post_id' => $data['post_id'] ?? null,
            'brand' => $data['brand'] ?? null,
            'year' => $data['year'] ?? null,
            'color' => $data['color'] ?? null,
            'amount' => $data['amount'] ?? null,
            'fuel' => $data['fuelType'] ?? null,
            'transmission' => $data['transmission'] ?? null,
            'km_driven' => $data['kmDriven'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            'no_of_owner' => $data['owners'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::create($restructuredData);
    }
}
