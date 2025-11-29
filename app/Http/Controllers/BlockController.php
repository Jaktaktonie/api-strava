<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlockController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $blockedIds = UserBlock::query()
            ->where('blocker_id', $userId)
            ->pluck('blocked_id');

        $users = User::query()->whereIn('id', $blockedIds)->get();

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ], [], ['user_id' => 'user']);

        $blockerId = $request->user()->id;
        $blockedId = (int) $data['user_id'];

        if ($blockerId === $blockedId) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Nie możesz zablokować siebie.');
        }

        if (UserBlock::existsBetween($blockerId, $blockedId)) {
            return response()->json(['status' => 'already_blocked'], Response::HTTP_OK);
        }

        // Usuń ewentualne zaproszenia/relacje znajomych w obu kierunkach.
        FriendRequest::query()
            ->where(function ($q) use ($blockerId, $blockedId) {
                $q->where('sender_id', $blockerId)->where('receiver_id', $blockedId);
            })
            ->orWhere(function ($q) use ($blockerId, $blockedId) {
                $q->where('sender_id', $blockedId)->where('receiver_id', $blockerId);
            })
            ->delete();

        UserBlock::create([
            'blocker_id' => $blockerId,
            'blocked_id' => $blockedId,
        ]);

        return response()->json(['status' => 'blocked'], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, User $user)
    {
        $blockerId = $request->user()->id;

        UserBlock::query()
            ->where('blocker_id', $blockerId)
            ->where('blocked_id', $user->id)
            ->delete();

        return response()->json(['status' => 'unblocked']);
    }
}
