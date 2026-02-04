<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\InviteToken;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'last_activity',
        'phone_no',
        'password',
        'address',
        'latitude',
        'longitude',
        'about_me',
        'otp_resend_count',
        'otp_sent_at',
        'last_otp_resend_at',
        'joined_via_invite',
        'admin_role',
        'created_by_admin_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_sent_at' => 'datetime',
        'last_otp_resend_at' => 'datetime',
        'last_activity' => 'datetime',
        'joined_via_invite' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function buyerChats()
    {
        return $this->hasMany(Chat::class, 'buyer_id', 'id');
    }

    // A User can have many Chats as a Seller
    public function sellerChats()
    {
        return $this->hasMany(Chat::class, 'seller_id', 'id');
    }

    public function images()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Get the users that this user follows.
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'follower_id', 'following_id');
    }

    /**
     * Get the users that follow this user.
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'following_id', 'follower_id');
    }

    public function followedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_followers', 'user_id', 'post_id')
            ->withTimestamps();
    }

    public function companyDetail()
    {
        return $this->hasOne(CompanyDetail::class, 'users_id');
    }

    // Add this relationship
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    // Update the route method
    public function routeNotificationForFirebase($notification)
    {
        return $this->deviceTokens()->pluck('token')->toArray();
    }

    /**
     * Get invite tokens owned by this user
     */
    public function inviteTokens()
    {
        return $this->hasMany(InviteToken::class, 'user_id');
    }

    /**
     * Get invite tokens used by this user
     */
    public function usedInviteTokens()
    {
        return $this->hasMany(InviteToken::class, 'used_by_user_id');
    }

    // --- Admin roles & permissions ---

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function createdAdmins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'created_by_admin_id');
    }

    public function isAdmin(): bool
    {
        return $this->admin_role !== null;
    }

    public function isSuperAdmin(): bool
    {
        return $this->admin_role === 'super_admin';
    }

    public function isLead(): bool
    {
        return $this->admin_role === 'lead';
    }

    public function isSupervisor(): bool
    {
        return $this->admin_role === 'supervisor';
    }

    /** Check if user can access a permission by slug (super_admin has all) */
    public function hasPermissionTo(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->permissions()->where('slug', $slug)->exists();
    }

    /** Whether this admin user is marked as "invited" (lead/supervisor). Uses joined_via_invite. Invited admins only see/add invited users. */
    public function isInvitedAdmin(): bool
    {
        return $this->isAdmin() && (bool) $this->joined_via_invite;
    }
}
