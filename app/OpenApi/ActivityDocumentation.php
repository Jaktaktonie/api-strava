<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

class ActivityDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="List authenticated user activities",
     *     tags={"Activities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by activity type (run/ride/walk)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Filter by start date (ISO 8601)",
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Filter by end date (ISO 8601)",
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated activity list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ActivityResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/activities",
     *     summary="Store new activity",
     *     tags={"Activities"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ActivityRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created activity",
     *         @OA\JsonContent(ref="#/components/schemas/ActivityResource")
     *     )
     * )
     */
    public function store()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/activities/{id}",
     *     summary="Show activity details",
     *     tags={"Activities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity data",
     *         @OA\JsonContent(ref="#/components/schemas/ActivityResource")
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function show()
    {
    }

    /**
     * @OA\Put(
     *     path="/api/activities/{id}",
     *     summary="Update activity",
     *     tags={"Activities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ActivityRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated activity",
     *         @OA\JsonContent(ref="#/components/schemas/ActivityResource")
     *     )
     * )
     */
    public function update()
    {
    }

    /**
     * @OA\Delete(
     *     path="/api/activities/{id}",
     *     summary="Delete activity",
     *     tags={"Activities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy()
    {
    }
}

/**
 * @OA\Schema(
 *     schema="ActivityResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="type", type="string", enum={"run","ride","walk"}),
 *     @OA\Property(property="start_time", type="string", format="date-time"),
 *     @OA\Property(property="end_time", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="duration_seconds", type="integer", nullable=true),
 *     @OA\Property(property="distance_meters", type="integer"),
 *     @OA\Property(property="distance_km", type="number", format="float"),
 *     @OA\Property(property="avg_speed_kmh", type="number", format="float", nullable=true),
 *     @OA\Property(property="avg_pace", type="number", format="float", nullable=true),
 *     @OA\Property(
 *         property="route",
 *         type="array",
 *         nullable=true,
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="lat", type="number", format="float"),
 *             @OA\Property(property="lng", type="number", format="float")
 *         )
 *     ),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="photo_url", type="string", nullable=true),
 *     @OA\Property(property="gpx_path", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class ActivitySchema
{
}

/**
 * @OA\Schema(
 *     schema="ActivityRequest",
 *     type="object",
 *     required={"title","type","start_time","distance_meters"},
 *     @OA\Property(property="title", type="string", example="Morning Run"),
 *     @OA\Property(property="type", type="string", enum={"run","ride","walk"}, example="run"),
 *     @OA\Property(property="start_time", type="string", format="date-time", example="2025-11-22T08:30:00Z"),
 *     @OA\Property(property="end_time", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="duration_seconds", type="integer", example=1800),
 *     @OA\Property(property="distance_meters", type="integer", example=5000),
 *     @OA\Property(property="avg_speed_kmh", type="number", format="float", example=10.2),
 *     @OA\Property(property="avg_pace", type="number", format="float", example=5.5),
 *     @OA\Property(
 *         property="route",
 *         type="array",
 *         nullable=true,
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="lat", type="number", format="float", example=52.1),
 *             @OA\Property(property="lng", type="number", format="float", example=21.0)
 *         )
 *     ),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Evening tempo workout"),
 *     @OA\Property(property="photo_url", type="string", nullable=true, example="https://cdn.mini-strava.dev/run.jpg"),
 *     @OA\Property(property="gpx_path", type="string", nullable=true, example="activities/1234.gpx")
 * )
 */
class ActivityRequestSchema
{
}
