<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLike;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KudosController extends Controller
{
    public function store(Request $request, Activity $activity)
    {
        $userId = $request->user()->id;

        ActivityLike::firstOrCreate([
            'activity_id' => $activity->id,
            'user_id' => $userId,
        ]);

        return $this->responsePayload($activity, $userId, Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Activity $activity)
    {
        $userId = $request->user()->id;

        ActivityLike::where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->delete();

        return $this->responsePayload($activity, $userId, Response::HTTP_OK);
    }

    protected function responsePayload(Activity $activity, int $userId, int $status)
    {
        $likesCount = ActivityLike::where('activity_id', $activity->id)->count();
        $likedByMe = ActivityLike::where('activity_id', $activity->id)->where('user_id', $userId)->exists();

        return response()->json([
            'likes_count' => $likesCount,
            'liked_by_me' => $likedByMe,
        ], $status);
    }
}
