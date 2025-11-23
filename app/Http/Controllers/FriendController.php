<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FriendController extends Controller
{
    public function friends(Request $request)
    {
        $userId = $request->user()->id;

        $friendIds = FriendRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->get()
            ->map(fn ($fr) => $fr->sender_id === $userId ? $fr->receiver_id : $fr->sender_id);

        $friends = User::query()
            ->whereIn('id', $friendIds)
            ->get();

        return UserResource::collection($friends);
    }

    public function requests(Request $request)
    {
        $user = $request->user();

        $incoming = $user->friendRequestsReceived()->where('status', 'pending')->with('sender')->get();
        $outgoing = $user->friendRequestsSent()->where('status', 'pending')->with('receiver')->get();

        return response()->json([
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ]);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ], [], ['user_id' => 'receiver']);

        $receiverId = (int) $request->input('user_id');
        $senderId = $request->user()->id;

        if ($receiverId === $senderId) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Nie możesz zaprosić samego siebie.');
        }

        $friendRequest = FriendRequest::updateOrCreate(
            ['sender_id' => $senderId, 'receiver_id' => $receiverId],
            ['status' => 'pending']
        );

        return response()->json([
            'status' => $friendRequest->status,
        ], Response::HTTP_CREATED);
    }

    public function accept(Request $request, FriendRequest $friendRequest)
    {
        $this->authorizeChange($request, $friendRequest);

        $friendRequest->update(['status' => 'accepted']);

        return response()->json(['status' => $friendRequest->status]);
    }

    public function reject(Request $request, FriendRequest $friendRequest)
    {
        $this->authorizeChange($request, $friendRequest);

        $friendRequest->update(['status' => 'rejected']);

        return response()->json(['status' => $friendRequest->status]);
    }

    protected function authorizeChange(Request $request, FriendRequest $friendRequest): void
    {
        abort_unless($friendRequest->receiver_id === $request->user()->id, Response::HTTP_FORBIDDEN);
    }
}
