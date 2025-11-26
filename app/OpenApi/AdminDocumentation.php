<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

class AdminDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="List users for admin panel",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="search", in="query", description="Filter by name or email", @OA\Schema(type="string")),
     *     @OA\Parameter(name="role", in="query", description="Filter by role", @OA\Schema(type="string")),
     *     @OA\Parameter(name="registered_from", in="query", description="Registered from (date)", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="registered_to", in="query", description="Registered to (date)", @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated users",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function usersIndex()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users/{id}",
     *     summary="Show user details with recent activities",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *             @OA\Property(
     *                 property="recent_activities",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ActivityResource")
     *             )
     *         )
     *     )
     * )
     */
    public function usersShow()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/admin/activities",
     *     summary="List activities with admin filters",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date-time")),
     *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date-time")),
     *     @OA\Parameter(name="min_distance", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="max_distance", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated activities",
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
    public function activitiesIndex()
    {
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/activities/{id}",
     *     summary="Delete activity as admin",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function activitiesDestroy()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/admin/stats",
     *     summary="Global statistics for admin dashboard",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="week, month or empty for totals",
     *         @OA\Schema(type="string", enum={"week","month"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistics payload",
     *         @OA\JsonContent(ref="#/components/schemas/AdminStats")
     *     )
     * )
     */
    public function stats()
    {
    }
}

/**
 * @OA\Schema(
 *     schema="AdminStats",
 *     type="object",
 *     @OA\Property(property="period", type="string"),
 *     @OA\Property(property="users_total", type="integer"),
 *     @OA\Property(property="activities_total", type="integer"),
 *     @OA\Property(property="distance_total_km", type="number", format="float"),
 *     @OA\Property(property="period_activities", type="integer", nullable=true),
 *     @OA\Property(property="period_distance_km", type="number", format="float", nullable=true)
 * )
 */
class AdminStatsSchema
{
}
