<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Image extends Model
{
    use HasFactory, HasUuids;

    use HasFactory;

    protected $fillable = ['url', 'imageable_id', 'imageable_type'];

    protected $hidden = [
        'imageable_type',
        'imageable_id',
        'created_at',
        'updated_at'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
