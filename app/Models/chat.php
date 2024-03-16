<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'sender_id',
        'receiver_id',
        'message'
    ];
}
