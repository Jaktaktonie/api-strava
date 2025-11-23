<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

class SocialDocumentation
{
    /**
     * @OA\Post(
     *     path="/api/friends/invite",
     *     summary="Send friend invite",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Invite sent")
     * )
     */
    public function invite()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/friends/{id}/accept",
     *     summary="Accept friend invite",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Accepted")
     * )
     */
    public function accept()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/friends/{id}/reject",
     *     summary="Reject friend invite",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Rejected")
     * )
     */
    public function reject()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/friends",
     *     summary="List friends",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Friends list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     )
     * )
     */
    public function friends()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/friends/requests",
     *     summary="List incoming and outgoing requests",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Requests")
     * )
     */
    public function requests()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/feed",
     *     summary="Feed with friends activities",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Feed entries",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/FeedActivity")
     *             )
     *         )
     *     )
     * )
     */
    public function feed()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/activities/{id}/kudos",
     *     summary="Give kudos to activity",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=201, description="Kudos added")
     * )
     */
    public function giveKudos()
    {
    }

    /**
     * @OA\Delete(
     *     path="/api/activities/{id}/kudos",
     *     summary="Remove kudos",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Kudos removed")
     * )
     */
    public function removeKudos()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/activities/{id}/comments",
     *     summary="List comments for activity",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Comments",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         )
     *     )
     * )
     */
    public function commentsIndex()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/activities/{id}/comments",
     *     summary="Add comment to activity",
     *     tags={"Social"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="content", type="string", example="Świetny bieg!")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comment added")
     * )
     */
    public function commentsStore()
    {
    }
}

/**
 * @OA\Schema(
 *     schema="FeedActivity",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="distance_km", type="number", format="float"),
 *     @OA\Property(property="duration_seconds", type="integer", nullable=true),
 *     @OA\Property(property="start_time", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="photo_url", type="string", nullable=true),
 *     @OA\Property(property="kudos_count", type="integer"),
 *     @OA\Property(property="comments_count", type="integer"),
 *     @OA\Property(property="liked_by_me", type="boolean"),
 *     @OA\Property(
 *         property="latest_comments",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Comment")
 *     )
 * )
 */
class FeedActivitySchema
{
}

/**
 * @OA\Schema(
 *     schema="Comment",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true)
 * )
 */
class CommentSchema
{
}
