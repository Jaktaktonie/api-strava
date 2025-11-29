<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserStatsController extends Controller
{
    public function me(Request $request)
    {
        [$from, $to, $label] = $this->periodRange($request->string('period')->lower()->value());

        $query = Activity::query()->where('user_id', $request->user()->id);

        if ($from) {
            $query->where('start_time', '>=', $from);
        }
        if ($to) {
            $query->where('start_time', '<=', $to);
        }

        $stats = (clone $query)
            ->selectRaw('COUNT(*) as workouts')
            ->selectRaw('COALESCE(SUM(distance_meters), 0) as distance_meters')
            ->selectRaw('COALESCE(SUM(duration_seconds), 0) as duration_seconds')
            ->first();

        $totalDistance = (float) ($stats->distance_meters ?? 0);
        $totalDuration = (int) ($stats->duration_seconds ?? 0);
        $avgSpeed = $totalDuration > 0 ? round(($totalDistance / 1000) / ($totalDuration / 3600), 2) : null;

        return response()->json([
            'period' => $label,
            'workouts' => (int) ($stats->workouts ?? 0),
            'distance_km' => round($totalDistance / 1000, 2),
            'duration_seconds' => (int) $totalDuration,
            'avg_speed_kmh' => $avgSpeed,
        ]);
    }

    public function ranking(Request $request)
    {
        [$from, $to, $label] = $this->periodRange($request->string('period')->lower()->value());

        $query = Activity::query();
        if ($from) {
            $query->where('start_time', '>=', $from);
        }
        if ($to) {
            $query->where('start_time', '<=', $to);
        }

        $totals = $query
            ->selectRaw('user_id, SUM(distance_meters) as distance_meters, COUNT(*) as workouts')
            ->groupBy('user_id')
            ->orderByDesc('distance_meters')
            ->limit(20)
            ->with('user')
            ->get();

        $formatted = $totals->map(function ($row, $index) {
            return [
                'position' => $index + 1,
                'user_id' => $row->user_id,
                'user' => [
                    'name' => $row->user?->name,
                    'avatar_url' => $row->user?->avatar_url,
                ],
                'distance_km' => round($row->distance_meters / 1000, 2),
                'workouts' => (int) $row->workouts,
            ];
        });

        return response()->json([
            'period' => $label,
            'ranking' => $formatted,
        ]);
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon, 2: string}
     */
    protected function periodRange(?string $period): array
    {
        return match ($period) {
            'week' => [Carbon::now()->subWeek(), null, 'week'],
            'month' => [Carbon::now()->subMonth(), null, 'month'],
            default => [null, null, 'all'],
        };
    }
}
