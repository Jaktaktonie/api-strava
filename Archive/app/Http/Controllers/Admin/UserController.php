<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withCount('activities')->latest();

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->toString()) {
            $query->where('role', $role);
        }

        if ($from = $request->input('registered_from')) {
            $query->where('created_at', '>=', Carbon::parse($from));
        }

        if ($to = $request->input('registered_to')) {
            $query->where('created_at', '<=', Carbon::parse($to));
        }

        return UserResource::collection(
            $query->paginate()->withQueryString()
        );
    }

    public function show(User $user)
    {
        $user->loadCount('activities');

        $recentActivities = $user->activities()
            ->latest('start_time')
            ->limit(5)
            ->get();

        return response()->json([
            'user' => new UserResource($user),
            'recent_activities' => ActivityResource::collection($recentActivities),
        ]);
    }
}
