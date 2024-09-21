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


    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'post_id' => $data['post_id'] ?? null,
            'brand' => $data['brand'] ?? null,
            'year' => $data['year'] ?? null,
            'km_driven' => $data['km_driven'] ?? null,
            'amount' => $data['amount'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::create($restructuredData);
    }
}
