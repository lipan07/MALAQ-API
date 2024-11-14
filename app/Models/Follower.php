<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $table = 'followers';

    public $timestamps = true; // If you are using the created_at and updated_at timestamps

    protected $fillable = ['user_id', 'follower_id']; // Fillable attributes

    // User being followed
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // User who follows
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
