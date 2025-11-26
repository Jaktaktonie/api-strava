<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Activity::factory()->count(3)->create(['distance_meters' => 5000]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/stats?period=week');

        $response->assertOk()
            ->assertJsonFragment(['users_total' => User::count()])
            ->assertJsonFragment(['activities_total' => 3])
            ->assertJsonFragment(['period' => 'week']);
    }
}
