<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $this->deleteStoredAvatar($user->getRawOriginal('avatar_url'));
            $path = $request->file('avatar')->storePublicly("avatars/{$user->id}", 'public');
            $data['avatar_url'] = $path;
        } elseif (array_key_exists('avatar_url', $data)) {
            if ($data['avatar_url'] === null) {
                $this->deleteStoredAvatar($user->getRawOriginal('avatar_url'));
            }

            unset($data['avatar_url']);
        }

        $user->fill($data);
        $user->name = trim("{$user->first_name} {$user->last_name}");
        $user->save();

        return new UserResource($user);
    }

    private function deleteStoredAvatar(?string $value): void
    {
        if (! $value) {
            return;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/storage/', 'storage/'])) {
            return;
        }

        Storage::disk('public')->delete($value);
    }
}
