<?php

namespace App\Http\Requests\Activity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'string', 'in:run,ride,walk'],
            'start_time' => ['sometimes', 'required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'distance_meters' => ['sometimes', 'required', 'integer', 'min:0'],
            'avg_speed_kmh' => ['nullable', 'numeric', 'min:0'],
            'avg_pace' => ['nullable', 'numeric', 'min:0'],
            'route' => ['nullable', 'array'],
            'route.*.lat' => ['required_with:route', 'numeric'],
            'route.*.lng' => ['required_with:route', 'numeric'],
            'notes' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'url'],
            'gpx_path' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
