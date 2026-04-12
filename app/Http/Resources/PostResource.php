<?php

namespace App\Http\Resources;

use App\Enums\PostContentType;
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

        // Default: assume it's a list for performance (no video URLs in list payload)
        return true;
    }

    public function toArray(Request $request): array
    {
        $isListContext = $this->isListContext($request);
        $backblazeService = app(BackblazeService::class);

        $imageUrls = [];
        $videoUrls = [];
        $mediaPayload = [];

        if ($this->relationLoaded('contents')) {
            $sorted = $this->contents->sortBy('sort_order')->values();
            foreach ($sorted as $c) {
                if ($c->type === PostContentType::Image) {
                    $imageUrls[] = $c->url;
                } elseif ($c->type === PostContentType::Video) {
                    $url = $backblazeService->getSignedUrl($c->url) ?? $c->url;
                    $videoUrls[] = $url;
                }
                if (!$isListContext) {
                    $mediaPayload[] = [
                        'id' => $c->id,
                        'type' => $c->type->value,
                        'backblaze_id' => $c->backblaze_id,
                        'url' => $c->type === PostContentType::Video
                            ? ($backblazeService->getSignedUrl($c->url) ?? $c->url)
                            : $c->url,
                    ];
                }
            }
        }

        $hasVideo = (bool) ($this->resource->getAttribute('has_video') ?? false);
        if (!$hasVideo && $this->relationLoaded('contents')) {
            $hasVideo = $this->contents->contains(fn ($c) => $c->type === PostContentType::Video);
        }

        if ($isListContext) {
            $videoData = [];
        } else {
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
            'images' => $imageUrls,
            'videos' => $videoData,
            'has_video' => $hasVideo,
            'media' => $isListContext ? [] : $mediaPayload,
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
