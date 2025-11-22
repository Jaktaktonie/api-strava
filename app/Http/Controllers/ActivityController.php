<?php

namespace App\Http\Controllers;

use App\Http\Requests\Activity\StoreActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

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
        $activity = $request->user()
            ->activities()
            ->create($request->validated());

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

        $activity->update($request->validated());

        return new ActivityResource($activity);
    }

    public function destroy(Request $request, Activity $activity)
    {
        $this->authorizeOwnership($request->user()->id, $activity);

        $activity->delete();

        return response()->noContent();
    }

    protected function authorizeOwnership(int $userId, Activity $activity): void
    {
        abort_unless($activity->user_id === $userId, Response::HTTP_FORBIDDEN);
    }
}
