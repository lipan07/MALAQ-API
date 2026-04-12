<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostContent extends Model
{
    use HasUuids;

    protected $table = 'post_contents';

    protected $fillable = [
        'post_id',
        'type',
        'backblaze_id',
        'url',
        'sort_order',
    ];

    protected $casts = [
        // Store as plain string so bad/legacy rows never crash hydration (enum cast throws ValueError).
        'type' => 'string',
        'sort_order' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
