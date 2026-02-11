<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['reporting_user_id', 'post_id', 'description', 'type'];

    /** User who submitted the report */
    public function reportingUser()
    {
        return $this->belongsTo(User::class, 'reporting_user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
