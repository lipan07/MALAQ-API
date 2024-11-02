<?php

namespace App\Models;

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
        'description',
        'amount',
    ];

    protected $casts = [
        'brand' => 'string',
    ];


    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'brand' => $data['brand'] ?? null,
            'year' => $data['year'] ?? null,
            'km_driven' => $data['km_driven'] ?? null,
            'amount' => $data['amount'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
