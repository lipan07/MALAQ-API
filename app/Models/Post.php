<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\PostType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'address',
        'post_time',
        'latitude',
        'longitude',
        'status',
        'type',
    ];

    protected $casts = [
        'type' => PostType::class,
        'status' => PostStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function mobile()
    {
        return $this->hasOne(PostMobile::class, 'post_id');
    }

    public function car()
    {
        return $this->hasOne(PostCar::class, 'post_id');
    }

    public function housesApartment()
    {
        return $this->hasOne(PostHousesApartment::class, 'post_id');
    }

    public function landPlots()
    {
        return $this->hasOne(PostLandPlot::class, 'post_id');
    }

    public function fashion()
    {
        return $this->hasOne(PostFashion::class, 'post_id');
    }

    public function bikes()
    {
        return $this->hasOne(PostBike::class, 'post_id');
    }

    public function jobs()
    {
        return $this->hasOne(PostJob::class, 'post_id');
    }

    public function pets()
    {
        return $this->hasOne(PostPet::class, 'post_id');
    }

    public function furnitures()
    {
        return $this->hasOne(PostFurniture::class, 'post_id');
    }

    public function electronicsAppliances()
    {
        return $this->hasOne(PostElectronicsAppliance::class, 'post_id');
    }

    public function others()
    {
        return $this->hasOne(PostOther::class, 'post_id');
    }

    public function shopOffices()
    {
        return $this->hasOne(PostShopOffice::class, 'post_id');
    }

    public function pgGuestHouses()
    {
        return $this->hasOne(PostPgGuestHouse::class, 'post_id');
    }

    public function accessories()
    {
        return $this->hasOne(PostAccessories::class, 'post_id');
    }

    public function commercialHeavyVehicles()
    {
        return $this->hasOne(PostHeavyVehicle::class, 'post_id');
    }

    public function commercialHeavyMachinery()
    {
        return $this->hasOne(PostHeavyMachinery::class, 'post_id');
    }

    public function books()
    {
        return $this->hasOne(PostBook::class, 'post_id');
    }

    public function sportsInstruments()
    {
        return $this->hasOne(PostSportHobby::class, 'post_id');
    }

    public function services()
    {
        return $this->hasOne(PostService::class, 'post_id');
    }

    public function vehicleSpareParts()
    {
        return $this->hasOne(PostVehicleSpareParts::class, 'post_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'post_followers', 'post_id', 'user_id')
            ->withTimestamps();
    }
}
