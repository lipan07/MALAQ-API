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
        'post_id',
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

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'salary_period' => $data['salaryPeriod'] ?? null,
            'position_type' => $data['positionType'] ?? null,
            'salary_from' => $data['salaryFrom'] ?? null,
            'salary_to' => $data['salaryTo'] ?? null,
            'title' => $data['adTitle'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
