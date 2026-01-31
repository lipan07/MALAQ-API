<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'post_id',
        'amount',
        'payment_method',
        'screenshot_path',
        'street_address',
        'city',
        'pin_code',
        'country',
        'status',
        'admin_verified_at',
        'admin_verified_by',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function adminVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_verified_by');
    }
}
