<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostVehicleSpareParts extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'type',
        'title',
        'amount',
        'description'
    ];

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'type' => $data['type'] ?? null,
            'amount' => $data['amount'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
