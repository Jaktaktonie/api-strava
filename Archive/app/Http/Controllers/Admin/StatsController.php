<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbuseReport;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\ActivityLike;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatsController extends Controller
{
    public function __invoke(Request $request)
    {
        $period = $request->string('period')->lower()->value();
        $periodRangeStart = match ($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            default => null,
        };
        $periodLabel = $periodRangeStart ? $period : 'all';

        $periodQuery = Activity::query();
        if ($periodRangeStart) {
            $periodQuery->where('start_time', '>=', $periodRangeStart);
        }

        $totalStats = Activity::query()
            ->selectRaw('COUNT(*) as workouts')
            ->selectRaw('COALESCE(SUM(distance_meters), 0) as distance_meters')
            ->selectRaw('COALESCE(SUM(duration_seconds), 0) as duration_seconds')
            ->first();
        $totalDistanceMeters = (float) ($totalStats->distance_meters ?? 0);
        $totalDurationSeconds = (int) ($totalStats->duration_seconds ?? 0);
        $totalAvgSpeed = $totalDurationSeconds > 0
            ? round(($totalDistanceMeters / 1000) / ($totalDurationSeconds / 3600), 2)
            : null;

        $periodStats = $periodRangeStart
            ? (clone $periodQuery)
                ->selectRaw('COUNT(*) as workouts')
                ->selectRaw('COALESCE(SUM(distance_meters), 0) as distance_meters')
                ->selectRaw('COALESCE(SUM(duration_seconds), 0) as duration_seconds')
                ->first()
            : $totalStats;
        $periodDistanceMeters = (float) ($periodStats?->distance_meters ?? 0);
        $periodDurationSeconds = (int) ($periodStats?->duration_seconds ?? 0);
        $periodAvgSpeed = $periodDurationSeconds > 0
            ? round(($periodDistanceMeters / 1000) / ($periodDurationSeconds / 3600), 2)
            : null;

        return response()->json([
            'period' => $periodLabel,
            'users_total' => User::count(),
            'users_new_period' => $periodRangeStart
                ? User::query()->where('created_at', '>=', $periodRangeStart)->count()
                : User::count(),
            'activities_total' => (int) ($totalStats->workouts ?? 0),
            'distance_total_km' => round($totalDistanceMeters / 1000, 2),
            'duration_total_seconds' => $totalDurationSeconds,
            'avg_speed_total_kmh' => $totalAvgSpeed,
            'period_activities' => (int) ($periodStats->workouts ?? 0),
            'period_distance_km' => round($periodDistanceMeters / 1000, 2),
            'period_duration_seconds' => $periodDurationSeconds,
            'period_avg_speed_kmh' => $periodAvgSpeed,
            'likes_total' => ActivityLike::count(),
            'likes_period' => $periodRangeStart
                ? ActivityLike::query()->where('created_at', '>=', $periodRangeStart)->count()
                : ActivityLike::count(),
            'comments_total' => ActivityComment::count(),
            'comments_period' => $periodRangeStart
                ? ActivityComment::query()->where('created_at', '>=', $periodRangeStart)->count()
                : ActivityComment::count(),
            'reports_total' => AbuseReport::count(),
            'reports_period' => $periodRangeStart
                ? AbuseReport::query()->where('created_at', '>=', $periodRangeStart)->count()
                : AbuseReport::count(),
            'blocks_total' => UserBlock::count(),
            'blocks_period' => $periodRangeStart
                ? UserBlock::query()->where('created_at', '>=', $periodRangeStart)->count()
                : UserBlock::count(),
            'friend_requests_total' => FriendRequest::count(),
            'friend_requests_period' => $periodRangeStart
                ? FriendRequest::query()->where('created_at', '>=', $periodRangeStart)->count()
                : FriendRequest::count(),
        ]);
    }
}
