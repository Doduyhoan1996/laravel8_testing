<?php

/**
 * Test tính năng Authentication
 *
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    // RefreshDatabase reset lại DB sau mỗi bài test
    use RefreshDatabase;

    /**
    * @var \App\Models\User
    */
    protected $user;

    /**
     * Khởi tạo các thuộc tính sẽ sử dụng nhiều khi trước khi bắt đầu test
     */
    public function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * test case có thể render view login thông qua HTTP request
     *
     */
    public function testLoginScreenCanBeRendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * test case user có thể login thông qua HTTP request
     */
    public function testUsersCanAuthenticateUsingTheLoginScreen()
    {
        $response = $this->post('/login', [
            'email' =>  $this->user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * test case user login lỗi thông qua HTTP request
     */
    public function testUsersCanNotAuthenticateWithInvalidPassword()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(302);

        //khẳng định tồn tại message lỗi được trả về khi nhập sai thông tin
        $response->assertSessionHasErrors([
            'email' => 'These credentials do not match our records.'
        ]);
        $this->assertGuest();
    }
}
