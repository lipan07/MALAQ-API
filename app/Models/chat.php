<?php

namespace App\Models;

use Database\Factories\ChatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/** PSR-4: App\Models\chat → app/Models/chat.php (required on case-sensitive Linux hosts). */
class chat extends Model
{
    use HasFactory, HasUuids;

    protected static function newFactory()
    {
        return ChatFactory::new();
    }

    protected $fillable = [
        'post_id',
        'buyer_id',
        'seller_id',
        'buyer_deleted_at',
        'seller_deleted_at',
    ];

    protected $casts = [
        'buyer_deleted_at' => 'datetime',
        'seller_deleted_at' => 'datetime',
    ];

    /**
     * Whether the given user has archived/deleted this chat on their side only.
     */
    public function isDeletedForUser(?string $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        if ((string) $this->buyer_id === (string) $userId) {
            return $this->buyer_deleted_at !== null;
        }
        if ((string) $this->seller_id === (string) $userId) {
            return $this->seller_deleted_at !== null;
        }

        return false;
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }

    /** Latest message per chat (safe for eager loading; do not use hasMany + limit(1) in with()). */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'chat_id', 'id')->latestOfMany('created_at');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id')->withTrashed();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
