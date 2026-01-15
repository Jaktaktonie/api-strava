<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);
        $response = $this->put('/api/profile', [
            'avatar' => $file,
        ]);

        $response->assertOk();

        $user->refresh();
        $path = $user->getRawOriginal('avatar_url');

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
        $expectedUrl = rtrim(config('app.url'), '/').'/api/avatars/'.$user->id;
        $response->assertJsonPath('data.avatar_url', $expectedUrl);
    }

    public function test_user_can_clear_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $path = "avatars/{$user->id}/old.jpg";

        Storage::disk('public')->put($path, 'old');
        $user->forceFill(['avatar_url' => $path])->save();

        Sanctum::actingAs($user);

        $this->putJson('/api/profile', [
            'avatar_url' => null,
        ])->assertOk()->assertJsonPath('data.avatar_url', rtrim(config('app.url'), '/').'/api/avatars/'.$user->id);

        $user->refresh();
        $this->assertSame($path, $user->getRawOriginal('avatar_url'));
        Storage::disk('public')->assertMissing($path);
    }
}
