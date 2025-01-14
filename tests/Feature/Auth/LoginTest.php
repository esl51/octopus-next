<?php

namespace Tests\Feature\Auth;

use App\Models\Access\User;
use App\Providers\AppServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public $csrfCookieUrl = '/sanctum/csrf-cookie';

    public $loginUrl = '/login';

    public $userUrl = '/api/auth/user';

    public $logoutUrl = '/logout';

    public $user;

    public $disabledUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->disabledUser = User::factory()->disabled()->create([]);
    }

    #[Test]
    public function can_see_login_page()
    {
        $this->get($this->loginUrl)
            ->assertSuccessful();
    }

    #[Test]
    public function can_get_csrf_cookie()
    {
        $this->get($this->csrfCookieUrl)
            ->assertSuccessful();
    }

    #[Test]
    public function can_log_in()
    {
        $this->postJson($this->loginUrl, [
            'email' => $this->user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
    }

    #[Test]
    public function can_not_log_in_with_invalid_password()
    {
        $this->postJson($this->loginUrl, [
            'email' => $this->user->email,
            'password' => 'wrong_password',
        ])
            ->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function can_not_log_in_already_authenticated()
    {
        $this->actingAs($this->user)
            ->postJson($this->loginUrl, [
                'email' => $this->user->email,
                'password' => 'password',
            ])
            ->assertJsonStructure(['error']);
    }

    #[Test]
    public function can_not_log_in_disabled()
    {
        $this->actingAs($this->disabledUser)
            ->postJson($this->loginUrl, [
                'email' => $this->disabledUser->email,
                'password' => 'password',
            ])
            ->assertJsonStructure(['error']);
    }

    #[Test]
    public function can_redirect_already_authenticated()
    {
        $response = $this->actingAs($this->user)
            ->post($this->loginUrl, [
                'email' => $this->user->email,
                'password' => 'password',
            ]);
        $response->assertRedirect(AppServiceProvider::HOME);
    }

    #[Test]
    public function can_fetch_user()
    {
        $this->actingAs($this->user)
            ->getJson($this->userUrl)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'name', 'email']]);
    }

    #[Test]
    public function can_not_fetch_user_unauthenticated()
    {
        $this->getJson($this->userUrl)
            ->assertStatus(401);
    }

    #[Test]
    public function can_log_out()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->logoutUrl);

        $this->assertGuest();
        $response->assertStatus(204);
    }

    #[Test]
    public function can_not_send_too_many_login_requests()
    {
        for ($i = 0; $i <= 100; $i++) {
            $request = $this->postJson($this->loginUrl, [
                'email' => 'test@test.org',
                'password' => 'wrong_password',
            ]);
        }
        $request->assertStatus(429);
    }
}
