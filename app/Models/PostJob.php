<?php

namespace App\Models;

use App\Enums\PositionType;
use App\Enums\SalaryPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostJob extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'salary_period',
        'position_type',
        'salary_from',
        'salary_to',
        'title',
        'description',
    ];

    protected $casts = [
        'salary_period' => SalaryPeriod::class,
        'position_type' => PositionType::class,
    ];
}
