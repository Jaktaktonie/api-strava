<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLike;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KudosController extends Controller
{
    public function store(Request $request, Activity $activity)
    {
        $userId = $request->user()->id;

        abort_if($activity->user_id === $userId, Response::HTTP_UNPROCESSABLE_ENTITY, 'Nie możesz dać kudosa własnej aktywności.');
        abort_if(UserBlock::existsBetween($userId, $activity->user_id), Response::HTTP_FORBIDDEN, 'Nie możesz reagować na aktywność zablokowanego użytkownika.');

        ActivityLike::firstOrCreate([
            'activity_id' => $activity->id,
            'user_id' => $userId,
        ]);

        return $this->responsePayload($activity, $userId, Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Activity $activity)
    {
        $userId = $request->user()->id;

        if (UserBlock::existsBetween($userId, $activity->user_id)) {
            abort(Response::HTTP_FORBIDDEN, 'Nie możesz reagować na aktywność zablokowanego użytkownika.');
        }

        ActivityLike::where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->delete();

        return $this->responsePayload($activity, $userId, Response::HTTP_OK);
    }

    protected function responsePayload(Activity $activity, int $userId, int $status)
    {
        $aggregate = ActivityLike::query()
            ->selectRaw('COUNT(*) as likes_count')
            ->selectRaw('SUM(user_id = ?) as liked_by_me', [$userId])
            ->where('activity_id', $activity->id)
            ->first();

        return response()->json([
            'likes_count' => (int) ($aggregate->likes_count ?? 0),
            'liked_by_me' => (bool) ($aggregate->liked_by_me ?? 0),
        ], $status);
    }
}
