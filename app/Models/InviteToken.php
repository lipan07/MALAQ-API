<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InviteToken extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'token',
        'used_by_user_id',
        'expires_at',
        'used_at',
        'is_used',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who owns this token
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who used this token
     */
    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    /**
     * Check if token is valid (not used, not expired, and active for registration)
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture() && $this->is_active;
    }

    /**
     * Generate a unique 7-digit token
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
