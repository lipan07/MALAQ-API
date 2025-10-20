<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostFashion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'gender',
        'description',
    ];

    protected $casts = [
        'gender' => Gender::class
    ];
}
