<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Chat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'buyer_id',
        'seller_id',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }

    // A Chat belongs to one Seller
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    // A Chat belongs to a Product
    public function product()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    /**
     * Get all of the images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
