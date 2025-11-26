<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FriendController extends Controller
{
    public function friends(Request $request)
    {
        $userId = $request->user()->id;

        $blockedIds = UserBlock::relatedIds($userId);
        $friendIds = FriendRequest::friendIdsFor($userId)
            ->reject(fn (int $id) => $blockedIds->contains($id));

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

        $receiverId = (int) $request->integer('user_id');
        $senderId = (int) $request->user()->id;

        if ($receiverId === $senderId) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Nie możesz zaprosić samego siebie.');
        }

        if (UserBlock::existsBetween($senderId, $receiverId)) {
            abort(Response::HTTP_FORBIDDEN, 'Zaproszenie zablokowane.');
        }

        $existing = FriendRequest::query()
            ->where(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
            })
            ->first();

        if ($existing && $existing->status === 'accepted') {
            abort(Response::HTTP_CONFLICT, 'Jesteście już znajomymi.');
        }

        // Jeśli druga strona wysłała już zaproszenie, zaakceptuj je automatycznie.
        if ($existing && $existing->sender_id === $receiverId && $existing->status === 'pending') {
            $existing->update(['status' => 'accepted']);

            return response()->json(['status' => 'accepted'], Response::HTTP_OK);
        }

        // Jeśli zaproszenie jest w toku w tym samym kierunku, nie twórz duplikatu.
        if ($existing && $existing->status === 'pending') {
            return response()->json(['status' => 'pending'], Response::HTTP_OK);
        }

        $friendRequest = FriendRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

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
