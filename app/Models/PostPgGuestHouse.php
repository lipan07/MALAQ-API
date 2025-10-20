<?php

namespace App\Models;

use App\Enums\PgGuestHousesType;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use function PHPUnit\Framework\isEmpty;

class PostPgGuestHouse extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'pg_type',
        'furnishing',
        'listed_by',
        'carpet_area',
        'car_parking',
        'is_meal_included',
        'description',
    ];

    protected $casts = [
        'pg_type' => PgGuestHousesType::class,
        'furnishing' => PropertyFurnishing::class,
        'listed_by' => PropertyListedBy::class,
        'is_meal_included' => 'boolean'
    ];


    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'pg_type' => $data['pgType'] ?? null,
            'furnishing' => $data['furnishing'] ?? null,
            'listed_by' => $data['listeBy'] ?? null,
            'carpet_area' => $data['carpetArea'] ?? null,
            'car_parking' => $data['carParking'] ?? null,
            'is_meal_included' => ($data['isMealIncluded'] == 'Yes' || empty($data['isMealIncluded'])) ? true : false,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
