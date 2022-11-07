<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Event;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Notification;
use Tests\TestCase;
use URL;

class VerifyEmailTest extends TestCase
{
    public $notificaitonUrl = '/email/verification-notification';
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        if (!in_array('email-verification', config('fortify.features'))) {
            $this->markTestSkipped('email verification is disabled');
        }

        $this->user = User::factory()->create([
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function can_request_verification_link()
    {
        Notification::fake();
        $this->actingAs($this->user)
            ->postJson($this->notificaitonUrl);
        Notification::assertSentTo($this->user, VerifyEmail::class);
    }

    /** @test */
    public function can_verify_email()
    {
        Event::fake();
        $verifyEmailUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );
        $response = $this->actingAs($this->user)
            ->get($verifyEmailUrl);
        Event::assertDispatched(Verified::class);
        $this->assertTrue($this->user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
    }

    /** @test */
    public function can_not_verify_email_with_invalid_hash()
    {
        $verifyEmailUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1('wrong_email')]
        );

        $this->actingAs($this->user)
            ->get($verifyEmailUrl);
        $this->assertFalse($this->user->fresh()->hasVerifiedEmail());
    }

    /** @test */
    public function can_not_verify_email_unauthenticated()
    {
        $verifyEmailUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );
        $response = $this->get($verifyEmailUrl);
        $response->assertRedirectContains('/login');
    }
}
