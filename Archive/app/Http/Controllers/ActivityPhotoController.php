<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityPhotoController extends Controller
{
    public function show(Request $request, Activity $activity): StreamedResponse|Response
    {
        abort_unless($activity->user_id === $request->user()->id, Response::HTTP_FORBIDDEN);

        $value = $activity->getRawOriginal('photo_url');

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
