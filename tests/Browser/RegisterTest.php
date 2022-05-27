<?php

/**
 * Test chức năng Register user trên trình duyệt
 */

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
    * @var \App\Models\User
    */
    protected $user;

    /**
     * Khởi tạo các thuộc tính sẽ sử dụng nhiều khi trước khi bắt đầu test
     */
    public function setUp(): void {
        // gọi lại setUp của parent
        parent::setUp();

        // Tạo dữ liệu User
        $this->user = [
            'name' => 'Register Name',
            'email' => 'test@gmail.com',
            'password' => 'password',
        ];
    }

    /**
     * Test Case đăng ký lỗi với khi email đã tồn tại
     */
    public function testUserRegisterFailOnRegisterPage()
    {
        $user = User::factory()->create() ;
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/register')
                    ->assertSee(__('Register'))
                    ->type('name', $user->name)
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password fake')
                    ->press('Register')
                    ->assertPathIs('/register')
                    ->assertSee(__('The email has already been taken.'))
                    ->assertSee(__('The password confirmation does not match.'));
        });
    }

    /**
     * Test Case đăng ký thành công trên trình duyệt
     */
    public function testUserCanRegisterOnRegisterPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->assertSee(__('Register'))
                    ->type('name', $this->user['name'])
                    ->type('email', $this->user['email'])
                    ->type('password', $this->user['password'])
                    ->type('password_confirmation', $this->user['password'])
                    ->press('Register')
                    ->assertPathIs('/home');
        });
        $user = User::first();
        // khẳng định thông tin user đăng ký giống với trong CSDL
        $this->assertEquals($this->user['name'], $user->name);
        $this->assertEquals($this->user['email'], $user->email);
    }

}
