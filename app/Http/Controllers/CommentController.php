<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Activity;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Activity $activity)
    {
        $comments = $activity->comments()
            ->with('user')
            ->latest()
            ->paginate()
            ->withQueryString();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Activity $activity)
    {
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
