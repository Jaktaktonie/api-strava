<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedActivityResource;
use App\Models\Activity;
use App\Models\FriendRequest;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $friendIds = FriendRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->get()
            ->map(fn ($fr) => $fr->sender_id === $userId ? $fr->receiver_id : $fr->sender_id)
            ->push($userId);

        $activities = Activity::query()
            ->with(['user'])
            ->withCount(['likes', 'comments'])
            ->whereIn('user_id', $friendIds)
            ->latest('start_time')
            ->paginate()
            ->withQueryString();

        $activities->getCollection()->load([
            'likes' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            },
            'comments' => function ($q) {
                $q->latest()->limit(2)->with('user');
            },
        ]);

        return FeedActivityResource::collection($activities);
    }
}
