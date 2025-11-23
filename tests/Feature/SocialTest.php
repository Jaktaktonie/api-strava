<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SocialTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_invite_and_accept_friend(): void
    {
        Sanctum::actingAs($sender = User::factory()->create());
        $receiver = User::factory()->create();

        $inviteResponse = $this->postJson('/api/friends/invite', ['user_id' => $receiver->id]);
        $inviteResponse->assertCreated();

        Sanctum::actingAs($receiver);
        $request = FriendRequest::first();
        $this->postJson("/api/friends/{$request->id}/accept")->assertOk();

        $friends = $this->getJson('/api/friends')
            ->assertOk()
            ->json('data');

        $this->assertEquals($sender->id, $friends[0]['id']);
    }

    public function test_feed_shows_friends_activity(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $friend = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $friend->id]);

        FriendRequest::create([
            'sender_id' => $user->id,
            'receiver_id' => $friend->id,
            'status' => 'accepted',
        ]);

        $response = $this->getJson('/api/feed')
            ->assertOk();

        $response->assertJsonFragment(['id' => $activity->id]);
    }

    public function test_user_can_like_and_unlike_activity(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $activity = Activity::factory()->create();

        $this->postJson("/api/activities/{$activity->id}/kudos")
            ->assertCreated()
            ->assertJsonFragment(['liked_by_me' => true]);

        $this->deleteJson("/api/activities/{$activity->id}/kudos")
            ->assertOk()
            ->assertJsonFragment(['liked_by_me' => false]);
    }

    public function test_user_can_comment_on_activity(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $activity = Activity::factory()->create();

        $this->postJson("/api/activities/{$activity->id}/comments", [
            'content' => 'Super bieg!',
        ])->assertCreated()
          ->assertJsonFragment(['content' => 'Super bieg!']);

        $this->getJson("/api/activities/{$activity->id}/comments")
            ->assertOk()
            ->assertJsonFragment(['content' => 'Super bieg!']);
    }
}
