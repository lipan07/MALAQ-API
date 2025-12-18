<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            // 'buyer_id' => $this->buyer_id,
            // 'seller_id' => $this->seller_id,
            // 'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'buyer' => $this->buyer,
            'post' => $this->post ? array_merge(
                $this->post->toArray(),
                [
                    'image' => is_array($this->post->images) && count($this->post->images) > 0 
                        ? ['url' => $this->post->images[0]] 
                        : null
                ]
            ) : null,
        ];
    }
}
