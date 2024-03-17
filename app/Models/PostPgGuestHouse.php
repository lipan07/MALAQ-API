<?php

namespace App\Models;

use App\Enums\PgGuestHousesType;
use App\Enums\PropertyFurnishing;
use App\Enums\PropertyListedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostPgGuestHouse extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'pg_type',
        'furnishing',
        'listed_by',
        'carpet_area',
        'is_meal_included',
        'title',
        'description',
        'amount',
    ];

    protected $casts = [
        'pg_type' => PgGuestHousesType::class,
        'furnishing' => PropertyFurnishing::class,
        'listed_by' => PropertyListedBy::class,
        'is_meal_included' => 'boolean'
    ];
}
