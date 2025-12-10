<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

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
        'otp',
        'otp_resend_count',
        'otp_sent_at',
        'last_otp_resend_at',
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
}
