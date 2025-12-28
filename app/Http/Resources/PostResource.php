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
    /**
     * Static flag to indicate if we're in list/collection mode
     * This is set before creating the collection
     */
    public static $isListMode = false;

    /**
     * Check if this is a list/collection context (index method)
     * vs single resource context (show method)
     */
    private function isListContext(Request $request): bool
    {
        // First check static flag (set by collection method)
        if (self::$isListMode) {
            return true;
        }

        // Fallback: check route name
        $route = $request->route();
        if ($route) {
            $routeName = $route->getName();
            $routeAction = $route->getAction();

            // Check route name
            if ($routeName) {
                // Index routes
                if (
                    strpos($routeName, 'index') !== false ||
                    strpos($routeName, 'posts.index') !== false ||
                    strpos($routeName, 'myPost') !== false ||
                    strpos($routeName, 'sellersPost') !== false
                ) {
                    return true;
                }
                // Show/detail routes
                if (
                    strpos($routeName, 'show') !== false ||
                    strpos($routeName, 'posts.show') !== false
                ) {
                    return false;
                }
            }

            // Check controller action
            if (isset($routeAction['controller'])) {
                $controller = $routeAction['controller'];
                if (
                    strpos($controller, '@index') !== false ||
                    strpos($controller, '@myPost') !== false ||
                    strpos($controller, '@sellersPost') !== false
                ) {
                    return true;
                }
                if (strpos($controller, '@show') !== false) {
                    return false;
                }
            }
        }

        // Default: assume it's a list for performance (no signed URLs)
        return true;
    }

    public function toArray(Request $request): array
    {
        // Get images from posts table (JSON column)
        $imageUrls = $this->images ?? [];

        // For list/index context: just return boolean for video existence (no signed URLs)
        // For show/detail context: return full video URLs with signed URLs
        $isListContext = $this->isListContext($request);

        // Calculate has_video boolean - should be consistent for both list and show contexts
        $hasVideo = !empty($this->videos) && is_array($this->videos) && count($this->videos) > 0;

        if ($isListContext) {
            // List context: return empty array for videos, boolean for has_video - much faster!
            // No signed URL generation needed - saves significant time
            $videoData = []; // Empty array for list context
        } else {
            // Detail context: return full video URLs with signed URLs
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
            $videoData = $videoUrls;
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
            'videos' => $videoData, // Empty array for list, full URLs for detail
            'has_video' => $hasVideo, // Boolean indicating if post has videos (consistent for both list and show contexts)
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