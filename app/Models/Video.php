<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Video extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['url', 'videoable_id', 'videoable_type'];

    protected $hidden = [
        'videoable_type',
        'videoable_id',
        'created_at',
        'updated_at'
    ];

    public function videoable(): MorphTo
    {
        return $this->morphTo();
    }
}
