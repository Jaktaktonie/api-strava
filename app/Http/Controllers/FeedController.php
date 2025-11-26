<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedActivityResource;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\FriendRequest;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $blockedIds = UserBlock::relatedIds($userId);
        $friendIds = FriendRequest::friendIdsFor($userId)
            ->reject(fn (int $id) => $blockedIds->contains($id))
            ->push($userId);

        $activities = Activity::query()
            ->with(['user'])
            ->withCount(['likes', 'comments'])
            ->whereIn('user_id', $friendIds)
            ->whereNotIn('user_id', $blockedIds)
            ->latest('start_time')
            ->paginate()
            ->withQueryString();

        $activities->getCollection()->load([
            'likes' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            },
        ]);

        $this->loadLatestComments($activities->getCollection());

        return FeedActivityResource::collection($activities);
    }

    /**
     * Dołącza po 2 ostatnie komentarze na aktywność bez globalnego limitu.
     */
    protected function loadLatestComments(Collection $activities): void
    {
        $activityIds = $activities->pluck('id');

        if ($activityIds->isEmpty()) {
            return;
        }

        $comments = ActivityComment::query()
            ->with('user')
            ->whereIn('activity_id', $activityIds)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('activity_id')
            ->map(fn (Collection $items) => $items->take(2));

        $activities->each(function (Activity $activity) use ($comments): void {
            $activity->setRelation('comments', $comments->get($activity->id, collect()));
        });
    }
}
