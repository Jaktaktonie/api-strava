<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AvatarController extends Controller
{
    public function show(User $user): StreamedResponse|Response
    {
        $value = $user->getRawOriginal('avatar_url');

        if (! $value) {
            abort(404);
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return redirect()->away($value);
        }

        $path = $this->normalizeStoredPath($value);
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }

    private function normalizeStoredPath(string $value): string
    {
        if (Str::startsWith($value, '/storage/')) {
            return Str::after($value, '/storage/');
        }

        if (Str::startsWith($value, 'storage/')) {
            return Str::after($value, 'storage/');
        }

        return $value;
    }
}
