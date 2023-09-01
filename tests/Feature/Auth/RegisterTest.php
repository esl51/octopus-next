<?php

namespace Tests\Feature\Auth;

use App\Models\Access\User;
use Auth;
use Illuminate\Auth\Notifications\VerifyEmail;
use Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public $registerUrl = '/register';
    public $user;
    public $verificationEnabled = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!in_array('registration', config('fortify.features'))) {
            $this->markTestSkipped('registration is disabled');
        }

        $this->verificationEnabled = in_array('email-verification', config('fortify.features'));

        $this->user = User::factory()->create();
    }

    /** @test */
    public function can_see_register_page()
    {
        $this->get($this->registerUrl)
            ->assertSuccessful();
    }

    /** @test */
    public function can_register()
    {
        if ($this->verificationEnabled) {
            Notification::fake();
        }
        $this->postJson($this->registerUrl, [
            'name' => 'Test User',
            'email' => 'test@test.org',
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        if ($this->verificationEnabled) {
            $user = Auth::user();
            Notification::assertSentTo($user, VerifyEmail::class);
        }
    }

    /** @test */
    public function can_not_register_with_invalid_data()
    {
        $this->postJson($this->registerUrl, [
            'name' => null,
            'email' => 'bad_email',
            'password' => 'bad_pw',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => ['email' => [], 'name' => [], 'password' => []],
            ]);
        $this->assertGuest();
    }
}
