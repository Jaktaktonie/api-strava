<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Activity;
use App\Models\UserBlock;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Activity $activity)
    {
        if (UserBlock::existsBetween(auth()->id(), $activity->user_id)) {
            abort(403, 'Nie możesz przeglądać aktywności zablokowanego użytkownika.');
        }

        $comments = $activity->comments()
            ->with('user')
            ->latest()
            ->paginate()
            ->withQueryString();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Activity $activity)
    {
        if (UserBlock::existsBetween($request->user()->id, $activity->user_id)) {
            abort(403, 'Nie możesz komentować aktywności zablokowanego użytkownika.');
        }

        $comment = $activity->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->validated('content'),
        ]);

        $comment->load('user');

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}
