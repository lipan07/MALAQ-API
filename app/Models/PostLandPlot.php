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
}
