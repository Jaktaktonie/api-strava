<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'height_cm' => ['nullable', 'integer', 'between:50,260'],
            'weight_kg' => ['nullable', 'integer', 'between:20,400'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'locale' => ['nullable', 'string', 'in:pl,en'],
            'timezone' => ['nullable', 'string', 'max:60'],
        ];
    }
}
