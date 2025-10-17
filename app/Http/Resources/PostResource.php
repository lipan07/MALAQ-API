<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'type' => $this->type,
            'status' => $this->status,
            'show_phone' => $this->show_phone,
            'post_time' => $this->post_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->user,
            'category' => $this->category,
            'images' => $this->images->pluck('url'), // Get only the URL of each image
            'follower' => $this->follower ? true : false,
            'post_details' => $this->getPostDetails()
        ];
    }

    /**
     * Get post details efficiently by checking relationships in order of likelihood
     */
    private function getPostDetails()
    {
        // Use a more efficient approach with early returns
        $relationships = [
            'mobile', 'car', 'housesApartment', 'landPlots', 'fashion', 
            'bikes', 'jobs', 'pets', 'furnitures', 'electronicsAppliances', 
            'others', 'shopOffices', 'pgGuestHouses', 'accessories', 
            'commercialHeavyVehicles', 'commercialHeavyMachinery', 
            'books', 'sportsInstruments', 'services', 'vehicleSpareParts'
        ];

        foreach ($relationships as $relation) {
            if ($this->relationLoaded($relation) && $this->$relation) {
                return $this->$relation;
            }
        }

        return [];
    }
}
