<?php

namespace App\Http\Resources;

use App\Enums\PostContentType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $postPayload = null;
        if ($this->post) {
            if (!$this->post->relationLoaded('contents')) {
                $this->post->load(['contents' => function ($q) {
                    $q->orderBy('sort_order');
                }]);
            }
            $postArr = $this->post->toArray();
            unset($postArr['contents']);
            $firstImage = $this->post->contents->firstWhere('type', PostContentType::Image->value);
            $postPayload = array_merge($postArr, [
                'image' => $firstImage && $firstImage->url ? ['url' => $firstImage->url] : null,
            ]);
        }

        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'updated_at' => $this->updated_at,
            'post' => $postPayload,
        ];
    }
}
