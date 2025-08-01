<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'float',  // Explicitly cast to float
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
