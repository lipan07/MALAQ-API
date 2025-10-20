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
        'description',
    ];
    protected $hidden = ['post_id'];

    protected $casts = [
        'brand' => MobileBrand::class
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public static function restructureStoreData($data)
    {
        $restructuredData = [
            'brand' => $data['brand'] ?? null,
            'year' => $data['year'] ?? null,
            'description' => $data['description'] ?? null,
            // Add other fields you need to restructure or process
        ];

        // Save the restructured data
        return self::updateOrCreate(['post_id' => $data['post_id'] ?? null,], $restructuredData);
    }
}
