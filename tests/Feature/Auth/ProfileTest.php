<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Hash;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\UploadedFile;
use Notification;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public $updateAvatarUrl = '/api/auth/avatar';
    public $deleteAvatarUrl = '/api/auth/avatar';
    public $profileInformationUrl = '/user/profile-information';
    public $passwordUrl = '/user/password';
    public $user;
    public $verificationEnabled = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->verificationEnabled = in_array('email-verification', config('fortify.features'));

        $this->user = User::factory()->create();
    }

    /** @test */
    public function can_update_profile_information()
    {
        if ($this->verificationEnabled) {
            Notification::fake();
        }
        $this->actingAs($this->user)
            ->putJson($this->profileInformationUrl, [
                'name' => 'Test User',
                'email' => 'test@test.org',
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Test User',
            'email' => 'test@test.org',
        ]);
        if ($this->verificationEnabled) {
            Notification::assertSentTo($this->user, VerifyEmail::class);
        }
    }

    /** @test */
    public function can_update_password()
    {
        $this->actingAs($this->user)
            ->putJson($this->passwordUrl, [
                'current_password' => 'password',
                'password' => 'new_password',
            ])
            ->assertSuccessful();
        $this->assertTrue(Hash::check('new_password', $this->user->password));
    }

    /** @test */
    public function can_not_update_password_with_invalid_current()
    {
        $this->actingAs($this->user)
            ->putJson($this->passwordUrl, [
                'current_password' => 'wrong_password',
                'password' => 'new_password',
            ])
            ->assertJsonValidationErrors('current_password');
    }

    /** @test */
    public function can_update_avatar()
    {
        $this->actingAs($this->user)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 768, 1024),
            ])
            ->assertSuccessful();
        $this->storage->assertExists($this->user->avatar->path);
    }

    /** @test */
    public function can_delete_avatar()
    {
        $this->actingAs($this->user)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 1024, 768),
            ])
            ->assertSuccessful();
        $this->storage->assertExists($this->user->avatar->path);
        $avatarPath = $this->user->avatar->path;
        $this->deleteJson($this->updateAvatarUrl)
            ->assertSuccessful();
        $this->storage->assertMissing($avatarPath);
    }
}
