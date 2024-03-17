<?php

namespace App\Models;

use App\Enums\MobileBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostMobile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'brand',
        'year',
        'title',
        'description',
        'amount',
    ];
    protected $hidden = ['post_id'];

    protected $casts = [
        'brand' => MobileBrand::class
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
