<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PostFollower extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'post_id',  'post_user_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postCreator()
    {
        return $this->belongsTo(User::class, 'post_user_id');
    }
}
