<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostHousesApartment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'type',
        'bedrooms',
        'furnishing',
        'construction_status',
        'listed_by',
        'super_builtup_area',
        'carpet_area',
        'monthly_maintenance',
        'total_floors',
        'floor_no',
        'car_parking',
        'facing',
        'project_name',
        'title',
        'description',
        'amount',
    ];
}
