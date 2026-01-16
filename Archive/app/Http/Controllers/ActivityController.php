<?php

namespace App\Http\Controllers;

use App\Http\Requests\Activity\StoreActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()
            ->activities()
            ->latest('start_time');

        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

        if ($from = $request->input('from')) {
            $query->where('start_time', '>=', Carbon::parse($from));
        }

        if ($to = $request->input('to')) {
            $query->where('start_time', '<=', Carbon::parse($to));
        }

        return ActivityResource::collection(
            $query->paginate()->withQueryString()
        );
    }

    public function store(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $photo = $request->file('photo');

        unset($data['photo']);

        if ($photo) {
            unset($data['photo_url']);
        }

        $activity = $request->user()
            ->activities()
            ->create($data);

        if ($photo) {
            $activity->photo_url = $this->storePhoto($activity, $photo);
            $activity->save();
        }

        return (new ActivityResource($activity))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, Activity $activity): ActivityResource
    {
        $this->authorizeOwnership($request->user()->id, $activity);

        return new ActivityResource($activity);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): ActivityResource
    {
        $this->authorizeOwnership($request->user()->id, $activity);

        $data = $request->validated();
        $photo = $request->file('photo');

        unset($data['photo']);

        if ($photo) {
            $this->deleteStoredPhoto($activity->getRawOriginal('photo_url'));
            $data['photo_url'] = $this->storePhoto($activity, $photo);
        } elseif (array_key_exists('photo_url', $data)) {
            $this->deleteStoredPhoto($activity->getRawOriginal('photo_url'));

            if ($data['photo_url'] === null) {
                // Allow removing the photo without replacing it.
            }
        }

        $activity->update($data);

        return new ActivityResource($activity);
    }

    public function destroy(Request $request, Activity $activity)
    {
        $this->authorizeOwnership($request->user()->id, $activity);

        $this->deleteStoredPhoto($activity->getRawOriginal('photo_url'));
        $activity->delete();

        return response()->noContent();
    }

    public function export(Request $request, Activity $activity)
    {
        $this->authorizeOwnership($request->user()->id, $activity);

        $route = $activity->route ?? [];
        if (empty($route)) {
            abort(Response::HTTP_NOT_FOUND, 'Brak śladu GPS do eksportu.');
        }

        $gpx = $this->generateGpx($activity, $route);

        return response($gpx, Response::HTTP_OK, [
            'Content-Type' => 'application/gpx+xml',
            'Content-Disposition' => 'attachment; filename="activity-'.$activity->id.'.gpx"',
        ]);
    }

    protected function authorizeOwnership(int $userId, Activity $activity): void
    {
        abort_unless($activity->user_id === $userId, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param array<int, array{lat: float, lng: float}> $route
     */
    protected function generateGpx(Activity $activity, array $route): string
    {
        $startTime = $activity->start_time ?? Carbon::now();

        $trkPoints = collect($route)->values()->map(function (array $point, int $index) use ($startTime): string {
            $time = $startTime->copy()->addSeconds($index)->toIso8601String();
            $lat = $point['lat'];
            $lng = $point['lng'];

            return "        <trkpt lat=\"{$lat}\" lon=\"{$lng}\"><time>{$time}</time></trkpt>";
        })->implode("\n");

        $gpx = <<<GPX
<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="MiniStrava API" xmlns="http://www.topografix.com/GPX/1/1">
  <metadata>
    <name>{$activity->title}</name>
    <time>{$startTime->toIso8601String()}</time>
  </metadata>
  <trk>
    <name>{$activity->title}</name>
    <type>{$activity->type}</type>
    <trkseg>
{$trkPoints}
    </trkseg>
  </trk>
</gpx>
GPX;

        return $gpx;
    }

    private function storePhoto(Activity $activity, \Illuminate\Http\UploadedFile $photo): string
    {
        return $photo->storePublicly("activity-photos/{$activity->id}", 'public');
    }

    private function deleteStoredPhoto(?string $value): void
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
