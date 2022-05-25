<?php

/**
 * Test tính năng PasswordReset
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

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

    // test case có thể render view ResetPassword (màn hình lấy lại mật khẩu)
    public function testResetPasswordLinkScreenCanBeRendered()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
    }

    // test case có gửi Notification ResetPassword sau khi submit email
    public function testResetPasswordLinkCanBeRequested()
    {
        //fake Notification
        Notification::fake();

        $this->post('/password/email', ['email' => $this->user->email]);

        //assertSentTo khẳng định Notification ResetPassword đã được gửi cho user
        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    // test case có thể render view màn hình nhập mật khẩu mới
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

    // test case có thể thay đổi mật khẩu trên view màn hình nhập mật khẩu mới
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
