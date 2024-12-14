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
            'post_time' => $this->post_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => $this->category,
            'images' => $this->images->pluck('url'), // Get only the URL of each image
            'follower' => $this->follower ? true : false,
            'post_details' => $this->mobile ??
                $this->car ??
                $this->housesApartment ??
                $this->landPlots ??
                $this->fashion ??
                $this->bikes ??
                $this->jobs ??
                $this->pets ??
                $this->furnitures ??
                $this->electronicsAppliances ??
                $this->others ??
                $this->shopOffices ??
                $this->pgGuestHouses ??
                $this->accessories ??
                $this->commercialHeavyVehicles ??
                $this->commercialHeavyMachinery ??
                $this->books ??
                $this->sportsInstruments ??
                $this->services ??
                $this->vehicleSpareParts ?? []
        ];
    }
}
