<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Activity */
class FeedActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'type' => $this->type,
            'title' => $this->title,
            'distance_km' => $this->distance_meters ? round($this->distance_meters / 1000, 2) : 0,
            'duration_seconds' => $this->duration_seconds,
            'start_time' => optional($this->start_time)?->toIso8601String(),
            'photo_url' => $this->photo_url,
            'kudos_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'liked_by_me' => $this->likes?->isNotEmpty(),
            'latest_comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
