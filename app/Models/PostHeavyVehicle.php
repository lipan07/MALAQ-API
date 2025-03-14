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
        'brand',
        'year',
        'condition',
        'km_driven',
        'fuel_type',
        'owner',
        'listed_by',
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

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'brand' => $data['brand'] ?? null,
            'year' => $data['year'] ?? null,
            'condition' => $data['condition'] ?? null,
            'km_driven' => $data['kmDriven'] ?? null,
            'fuel_type' => $data['fuelType'] ?? null,
            'owner' => $data['owners'] ?? null,
            'listed_by' => $data['listedBy'] ?? null,
            'amount' => $data['amount'] ?? null,
            'description' => $data['description'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
