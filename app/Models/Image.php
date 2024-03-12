<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Image extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
