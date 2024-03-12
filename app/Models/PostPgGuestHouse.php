<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostPgGuestHouse extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'type',
        'furnishing',
        'listed_by',
        'carpet_area',
        'is_meal_included',
        'title',
        'description',
        'amount',
    ];
}
