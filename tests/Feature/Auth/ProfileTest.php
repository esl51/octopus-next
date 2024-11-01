<?php

namespace Tests\Feature\Auth;

use App\Models\Access\User;
use Hash;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\UploadedFile;
use Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public $updateAvatarUrl = '/api/auth/avatar';
    public $deleteAvatarUrl = '/api/auth/avatar';
    public $viewAvatarUrl = '/files/{id}/view';
    public $downloadAvatarUrl = '/files/{id}/download';
    public $profileInformationUrl = '/user/profile-information';
    public $passwordUrl = '/user/password';
    public $user;
    public $simpleUser;
    public $verificationEnabled = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->verificationEnabled = in_array('email-verification', config('fortify.features'));

        $this->user = User::factory()->create();
        $this->simpleUser = User::factory()->afterCreating(function ($model) {
            $model->assignRole('user');
        })->create();
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function can_not_update_password_with_invalid_current()
    {
        $this->actingAs($this->user)
            ->putJson($this->passwordUrl, [
                'current_password' => 'wrong_password',
                'password' => 'new_password',
            ])
            ->assertJsonValidationErrors('current_password');
    }

    #[Test]
    public function can_update_avatar()
    {
        $this->actingAs($this->user)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 768, 1024),
            ])
            ->assertSuccessful();
        $this->storage->assertExists($this->user->avatar->path);
    }

    #[Test]
    public function can_view_avatar()
    {
        $this->actingAs($this->user)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 768, 1024),
            ])
            ->assertSuccessful();
        $this->get(str_replace('{id}', $this->user->avatar->id, $this->viewAvatarUrl));
    }

    #[Test]
    public function can_download_avatar()
    {
        $this->actingAs($this->user)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 768, 1024),
            ])
            ->assertSuccessful();
        $this->get(str_replace('{id}', $this->user->avatar->id, $this->downloadAvatarUrl));
    }

    #[Test]
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

    #[Test]
    public function simple_user_can_not_list_not_owned_avatar()
    {
        $this->actingAs($this->user)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 768, 1024),
            ])
            ->assertSuccessful();
        $this->actingAs($this->simpleUser)
            ->getJson('/api/files')
            ->assertSuccessful()
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function simple_user_can_list_owned_avatar()
    {
        $this->actingAs($this->simpleUser)
            ->putJson($this->updateAvatarUrl, [
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 768, 1024),
            ])
            ->assertSuccessful();
        $this->actingAs($this->simpleUser)
            ->getJson('/api/files')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data');
    }
}
