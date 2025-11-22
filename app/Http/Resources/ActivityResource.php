<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Activity */
class ActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'type' => $this->type,
            'start_time' => optional($this->start_time)?->toIso8601String(),
            'end_time' => optional($this->end_time)?->toIso8601String(),
            'duration_seconds' => $this->duration_seconds,
            'distance_meters' => $this->distance_meters,
            'distance_km' => $this->distance_meters ? round($this->distance_meters / 1000, 2) : 0,
            'avg_speed_kmh' => $this->avg_speed_kmh,
            'avg_pace' => $this->avg_pace,
            'route' => $this->route,
            'notes' => $this->notes,
            'photo_url' => $this->photo_url,
            'gpx_path' => $this->gpx_path,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
