<?php

namespace Tests\Feature\Auth;

use App\Models\Access\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    public $forgotPasswordUrl = '/forgot-password';
    public $resetPasswordUrl = '/reset-password';
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        if (!in_array('reset-passwords', config('fortify.features'))) {
            $this->markTestSkipped('resetting passwords is disabled');
        }

        $this->user = User::factory()->create();
    }

    /** @test */
    public function can_see_forgot_password_page()
    {
        $this->get($this->forgotPasswordUrl)
            ->assertSuccessful();
    }

    /** @test */
    public function can_request_reset_password_link()
    {
        Notification::fake();
        $this->postJson($this->forgotPasswordUrl, [
            'email' => $this->user->email,
        ]);
        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    /** @test */
    public function can_see_reset_password_page()
    {
        Notification::fake();
        $this->postJson($this->forgotPasswordUrl, [
            'email' => $this->user->email,
        ]);
        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification) {
            $this->get($this->resetPasswordUrl . '/' . $notification->token)
                ->assertStatus(200);
            return true;
        });
    }

    /** @test */
    public function can_reset_password_with_valid_token()
    {
        Notification::fake();
        $this->postJson($this->forgotPasswordUrl, [
            'email' => $this->user->email,
        ]);
        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification) {
            $this->postJson($this->resetPasswordUrl, [
                'token' => $notification->token,
                'email' => $this->user->email,
                'password' => 'password',
            ])
                ->assertSuccessful();
            return true;
        });
    }

    /** @test */
    public function can_not_reset_password_with_invalid_token()
    {
        Notification::fake();
        $this->postJson($this->forgotPasswordUrl, [
            'email' => $this->user->email,
        ]);
        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification) {
            $this->postJson($this->resetPasswordUrl, [
                'token' => 'invalid_token',
                'email' => $this->user->email,
                'password' => 'password',
            ])
                ->assertStatus(422);
            return true;
        });
    }
}
