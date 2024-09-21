<?php

namespace App\Models;

use App\Enums\Condition;
use App\Enums\HeavyMachineryBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostHeavyMachinery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'title',
        'brand',
        'year',
        'condition',
        'fuel_type',
        'owner',
        'listed_by',
        'description',
        'amount',
        'contact_name',
        'contact_phone',
    ];

    protected $casts = [
        'brand' => HeavyMachineryBrand::class,
        'condition' => Condition::class,
    ];

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'post_id' => $data['post_id'] ?? null,
            'brand' => $data['brand'] ?? null,
            'year' => $data['year'] ?? null,
            'condition' => $data['condition'] ?? null,
            'fuel_type' => $data['fuelType'] ?? null,
            'owner' => $data['owners'] ?? null,
            'listed_by' => $data['listedBy'] ?? null,
            'amount' => $data['amount'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::create($restructuredData);
    }
}
