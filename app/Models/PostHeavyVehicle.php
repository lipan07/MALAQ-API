<?php

namespace App\Models;

use App\Enums\Condition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostHeavyVehicle extends Model
{
    use HasFactory;

    protected $casts = [
        'condition' => Condition::class,
    ];
}
