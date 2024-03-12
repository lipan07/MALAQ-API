<?php

namespace App\Models;

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
        'super_builtup_area',
        'carpet_area',
        'monthly_maintenance',
        'car_parking',
        'washroom',
        'project_name',
        'title',
        'description',
        'amount',
    ];
}
