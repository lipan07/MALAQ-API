<?php

namespace App\Models;

use App\Enums\PropertyFacing;
use App\Enums\PropertyListedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostLandPlot extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'listed_by',
        'carpet_area',
        'length',
        'breadth',
        'facing',
        'project_name',
        'title',
        'description',
        'amount',
    ];

    protected $casts = [
        'listed_by' => PropertyListedBy::class,
        'facing' => PropertyFacing::class
    ];

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'post_id' => $data['post_id'] ?? null,
            'listed_by' => $data['listedBy'] ?? null,
            'carpet_area' => $data['carpetArea'] ?? null,
            'facing' => $data['facing'] ?? null,
            'length' => $data['length'] ?? null,
            'breadth' => $data['breadth'] ?? null,
            'project_name' => $data['projectName'] ?? null,
            'amount' => $data['amount'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::create($restructuredData);
    }
}
