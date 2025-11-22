<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_activity(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $start = Carbon::now()->subHour();
        $payload = [
            'title' => 'Popołudniowy bieg',
            'type' => 'run',
            'start_time' => $start->toIso8601String(),
            'end_time' => $start->copy()->addMinutes(30)->toIso8601String(),
            'duration_seconds' => 1800,
            'distance_meters' => 5000,
            'avg_speed_kmh' => 10.0,
            'avg_pace' => 6.0,
            'route' => [
                ['lat' => 52.1, 'lng' => 21.0],
            ],
            'notes' => 'Pierwszy trening dnia',
        ];

        $response = $this->postJson('/api/activities', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Popołudniowy bieg')
            ->assertJsonPath('data.distance_meters', 5000);

        $this->assertDatabaseHas('activities', [
            'user_id' => $user->id,
            'title' => 'Popołudniowy bieg',
            'type' => 'run',
        ]);
    }

    public function test_user_can_list_only_their_activities(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $ownActivity = Activity::factory()->create([
            'user_id' => $user->id,
            'type' => 'run',
            'title' => 'Mój bieg',
        ]);
        $foreign = Activity::factory()->create(); // other users' activity

        $response = $this->getJson('/api/activities?type=run');

        $response
            ->assertOk()
            ->assertJsonFragment(['title' => 'Mój bieg'])
            ->assertJsonMissing(['title' => $foreign->title]);
    }

    public function test_user_cannot_view_foreign_activity(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $foreignActivity = Activity::factory()->create();

        $this->getJson("/api/activities/{$foreignActivity->id}")
            ->assertForbidden();
    }
}
