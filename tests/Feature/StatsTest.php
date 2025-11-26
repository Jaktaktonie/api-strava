<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_stats_return_totals(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        Activity::factory()->create([
            'user_id' => $user->id,
            'distance_meters' => 5000,
            'duration_seconds' => 1800,
            'start_time' => Carbon::now()->subDays(2),
        ]);

        $response = $this->getJson('/api/stats/me?period=week')
            ->assertOk()
            ->json();

        $this->assertSame('week', $response['period']);
        $this->assertSame(1, $response['workouts']);
        $this->assertEquals(5.0, $response['distance_km']);
        $this->assertSame(1800, $response['duration_seconds']);
    }

    public function test_ranking_returns_sorted_users(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        Activity::factory()->create(['user_id' => $u1->id, 'distance_meters' => 10000, 'start_time' => Carbon::now()]);
        Activity::factory()->create(['user_id' => $u2->id, 'distance_meters' => 5000, 'start_time' => Carbon::now()]);

        $response = $this->getJson('/api/stats/ranking?period=week')
            ->assertOk()
            ->json('ranking');

        $this->assertSame($u1->id, $response[0]['user_id']);
        $this->assertSame($u2->id, $response[1]['user_id']);
    }
}
