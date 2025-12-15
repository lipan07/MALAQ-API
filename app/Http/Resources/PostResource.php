<?php

namespace App\Http\Resources;

use App\Services\BackblazeService;
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
        // Get images from posts table (JSON column)
        $imageUrls = $this->images ?? [];
        
        // Get videos from posts table (JSON column) and generate signed URLs for Backblaze videos
        $videoUrls = [];
        if ($this->videos && is_array($this->videos) && count($this->videos) > 0) {
            $backblazeService = app(BackblazeService::class);
            $videoUrls = array_map(function ($url) use ($backblazeService) {
                if ($url && strpos($url, 'backblazeb2.com') !== false) {
                    return $backblazeService->getSignedUrl($url);
                }
                return $url;
            }, array_filter($this->videos));
        }

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'amount' => $this->amount,
            'view_count' => $this->view_count ?? 0,
            'like_count' => $this->like_count ?? 0,
            'type' => $this->type,
            'status' => $this->status,
            'show_phone' => (bool) $this->show_phone,
            'post_time' => $this->post_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->user,
            'category' => $this->category,
            'images' => $imageUrls, // Get images from posts table JSON column
            'videos' => $videoUrls, // Get signed URLs for videos from posts table JSON column
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