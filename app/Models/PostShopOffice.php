<?php

namespace App\Models;

use App\Enums\PropertyConstructionStatus;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostShopOffice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'furnishing',
        'listed_by',
        'construction_status',
        'super_builtup_area',
        'carpet_area',
        'monthly_maintenance',
        'car_parking',
        'washroom',
        'project_name',
        'description',
        'amount',
    ];
    protected $casts = [
        'furnishing' => PropertyFurnishing::class,
        'listed_by' => PropertyListedBy::class,
        'construction_status' => PropertyConstructionStatus::class,
    ];

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'furnishing' => $data['furnishing'] ?? null,
            'listed_by' => $data['listedBy'] ?? null,
            'construction_status' => $data['constructionStatus'] ?? null,
            'super_builtup_area' => $data['superBuiltUpArea'] ?? null,
            'carpet_area' => $data['carpetArea'] ?? null,
            'monthly_maintenance' => $data['maintenance'] ?? null,
            'car_parking' => $data['carParking'] ?? null,
            'washroom' => $data['washroom'] ?? null,
            'project_name' => $data['projectName'] ?? null,
            'amount' => $data['amount'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
