<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\ActivityLike;
use App\Models\AbuseReport;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->getJson('/api/admin/users')->assertForbidden();
    }

    public function test_admin_can_list_users_with_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['first_name' => 'Anna', 'last_name' => 'Nowak', 'email' => 'anna@example.com']);
        Activity::factory()->count(2)->create(['user_id' => $target->id]);
        User::factory()->create(['first_name' => 'John']);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/users?search=anna');

        $response
            ->assertOk()
            ->assertJsonFragment(['email' => 'anna@example.com'])
            ->assertJsonFragment(['activities_count' => 2]);
    }

    public function test_admin_can_delete_foreign_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::factory()->create();

        Sanctum::actingAs($admin);

        $this->deleteJson("/api/admin/activities/{$activity->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    public function test_admin_stats_endpoint_returns_totals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $activity = Activity::factory()->create([
            'user_id' => $userA->id,
            'distance_meters' => 5000,
            'duration_seconds' => 1800,
            'start_time' => Carbon::now(),
        ]);
        Activity::factory()->create([
            'user_id' => $userB->id,
            'distance_meters' => 10000,
            'duration_seconds' => 3600,
            'start_time' => Carbon::now()->subMonths(2),
        ]);
        ActivityLike::create(['activity_id' => $activity->id, 'user_id' => $userB->id]);
        ActivityComment::create(['activity_id' => $activity->id, 'user_id' => $userB->id, 'content' => 'Test']);
        FriendRequest::create(['sender_id' => $userA->id, 'receiver_id' => $userB->id, 'status' => 'pending']);
        UserBlock::create(['blocker_id' => $userA->id, 'blocked_id' => $userB->id]);
        AbuseReport::create([
            'reporter_id' => $userB->id,
            'reported_user_id' => $userA->id,
            'activity_id' => $activity->id,
            'reason' => 'Spam',
            'status' => 'open',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/stats?period=week');

        $response->assertOk()
            ->assertJsonFragment(['users_total' => User::count()])
            ->assertJsonFragment(['users_new_period' => 3])
            ->assertJsonFragment(['activities_total' => 2])
            ->assertJsonFragment(['period' => 'week']);
        $response->assertJsonFragment(['distance_total_km' => 15.0]);
        $response->assertJsonFragment(['duration_total_seconds' => 5400]);
        $response->assertJsonFragment(['avg_speed_total_kmh' => 10.0]);
        $response->assertJsonFragment(['period_activities' => 1]);
        $response->assertJsonFragment(['period_distance_km' => 5.0]);
        $response->assertJsonFragment(['period_duration_seconds' => 1800]);
        $response->assertJsonFragment(['period_avg_speed_kmh' => 10.0]);
        $response->assertJsonFragment(['likes_total' => 1]);
        $response->assertJsonFragment(['likes_period' => 1]);
        $response->assertJsonFragment(['comments_total' => 1]);
        $response->assertJsonFragment(['comments_period' => 1]);
        $response->assertJsonFragment(['friend_requests_total' => 1]);
        $response->assertJsonFragment(['friend_requests_period' => 1]);
        $response->assertJsonFragment(['blocks_total' => 1]);
        $response->assertJsonFragment(['blocks_period' => 1]);
        $response->assertJsonFragment(['reports_total' => 1]);
        $response->assertJsonFragment(['reports_period' => 1]);
    }
}
