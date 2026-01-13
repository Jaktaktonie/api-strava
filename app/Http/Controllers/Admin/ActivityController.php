<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->with('user')->latest('start_time');

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

        if ($from = $request->input('from')) {
            $query->where('start_time', '>=', Carbon::parse($from));
        }

        if ($to = $request->input('to')) {
            $query->where('start_time', '<=', Carbon::parse($to));
        }

        if ($minDistance = $request->input('min_distance')) {
            $query->where('distance_meters', '>=', (int) $minDistance);
        }

        if ($maxDistance = $request->input('max_distance')) {
            $query->where('distance_meters', '<=', (int) $maxDistance);
        }

        return ActivityResource::collection(
            $query->paginate()->withQueryString()
        );
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return response()->noContent();
    }
}
