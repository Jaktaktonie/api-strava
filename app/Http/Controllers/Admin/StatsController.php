<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
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

        $periodQuery = Activity::query();
        if ($periodRangeStart) {
            $periodQuery->where('start_time', '>=', $periodRangeStart);
        }

        $totalDistanceMeters = Activity::sum('distance_meters');
        $periodDistanceMeters = (clone $periodQuery)->sum('distance_meters');

        return response()->json([
            'period' => $periodRangeStart ? $period : 'all',
            'users_total' => User::count(),
            'activities_total' => Activity::count(),
            'distance_total_km' => round($totalDistanceMeters / 1000, 2),
            'period_activities' => $periodRangeStart ? $periodQuery->count() : null,
            'period_distance_km' => $periodRangeStart ? round($periodDistanceMeters / 1000, 2) : null,
        ]);
    }
}
