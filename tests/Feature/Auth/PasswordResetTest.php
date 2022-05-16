<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Providers\RouteServiceProvider;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @var \App\Models\User
    */
    protected $user;

    public function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testResetPasswordLinkScreenCanBeRendered()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
    }

    public function testResetPasswordLinkCanBeRequested()
    {
        Notification::fake();

        $this->post('/password/email', ['email' => $this->user->email]);

        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    public function testResetPasswordScreenCanBeRendered()
    {
        Notification::fake();

        $this->post('password/email', ['email' => $this->user->email]);

        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification) {
            $response = $this->get('/password/reset/'. $notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function testPasswordCanBeResetWithValidToken()
    {
        Notification::fake();

        $user = $this->user;

        $this->post('/password/email', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/password/reset', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new_password',
                'password_confirmation' => 'new_password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}
