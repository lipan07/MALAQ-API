<?php

namespace App\Models;

use App\Enums\Condition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostHeavyMachinery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'title',
        'brand',
        'model',
        'year',
        'condition',
        'hours_used',
        'description',
        'price',
        'contact_name',
        'contact_phone',
    ];

    protected $casts = [
        'condition' => Condition::class,
    ];
}
