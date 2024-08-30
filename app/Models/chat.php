<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'sender_id',
        'receiver_id',
        'message'
    ];

    /**
     * Get all of the images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
