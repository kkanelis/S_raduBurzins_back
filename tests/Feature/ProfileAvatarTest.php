<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_load_their_avatar_without_a_public_storage_url(): void
    {
        Storage::fake('public');

        $avatarPath = UploadedFile::fake()
            ->image('avatar.png', 100, 100)
            ->store('avatars', 'public');
        $user = User::factory()->create([
            'is_approved' => true,
            'avatar_path' => $avatarPath,
        ]);

        $this->actingAs($user)
            ->get('/api/profile/avatar')
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_avatar_endpoint_returns_not_found_when_no_avatar_is_set(): void
    {
        $user = User::factory()->create(['is_approved' => true]);

        $this->actingAs($user)
            ->getJson('/api/profile/avatar')
            ->assertNotFound();
    }
}
