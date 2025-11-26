<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

class StatsDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/stats/me",
     *     summary="Personal stats for period",
     *     tags={"Stats"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"week","month"})),
     *     @OA\Response(
     *         response=200,
     *         description="Stats",
     *         @OA\JsonContent(ref="#/components/schemas/UserStats")
     *     )
     * )
     */
    public function me()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/stats/ranking",
     *     summary="Ranking by distance for period",
     *     tags={"Stats"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"week","month"})),
     *     @OA\Response(
     *         response=200,
     *         description="Ranking",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="ranking",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/RankingEntry")
     *             )
     *         )
     *     )
     * )
     */
    public function ranking()
    {
    }
}

/**
 * @OA\Schema(
 *     schema="UserStats",
 *     type="object",
 *     @OA\Property(property="period", type="string"),
 *     @OA\Property(property="workouts", type="integer"),
 *     @OA\Property(property="distance_km", type="number", format="float"),
 *     @OA\Property(property="duration_seconds", type="integer"),
 *     @OA\Property(property="avg_speed_kmh", type="number", format="float", nullable=true)
 * )
 */
class UserStatsSchema
{
}

/**
 * @OA\Schema(
 *     schema="RankingEntry",
 *     type="object",
 *     @OA\Property(property="position", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="name", type="string", nullable=true),
 *         @OA\Property(property="avatar_url", type="string", nullable=true)
 *     ),
 *     @OA\Property(property="distance_km", type="number", format="float"),
 *     @OA\Property(property="workouts", type="integer")
 * )
 */
class RankingEntrySchema
{
}
