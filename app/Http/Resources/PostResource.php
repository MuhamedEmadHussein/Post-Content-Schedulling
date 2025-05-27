<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'scheduled_time' => $this->scheduled_time?->toIso8601String(),
            'status' => $this->status,
            'platforms' => PlatformResource::collection($this->whenLoaded('platforms')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}