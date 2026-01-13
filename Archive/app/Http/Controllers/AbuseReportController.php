<?php

namespace App\Http\Controllers;

use App\Models\AbuseReport;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AbuseReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:user,activity'],
            'target_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $reporterId = $request->user()->id;
        $reportedUserId = null;
        $activityId = null;

        if ($data['type'] === 'user') {
            $targetUser = User::findOrFail($data['target_id']);
            if ($targetUser->id === $reporterId) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Nie możesz zgłosić samego siebie.');
            }
            $reportedUserId = $targetUser->id;
        } else {
            $activity = Activity::findOrFail($data['target_id']);
            $activityId = $activity->id;
            $reportedUserId = $activity->user_id;
        }

        $report = AbuseReport::create([
            'reporter_id' => $reporterId,
            'reported_user_id' => $reportedUserId,
            'activity_id' => $activityId,
            'reason' => trim($data['reason']),
            'status' => 'open',
        ]);

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
        ], Response::HTTP_CREATED);
    }
}
