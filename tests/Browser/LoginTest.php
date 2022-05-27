<?php

/**
 * Test Màn hình login trên trình duyệt
 *
 */

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    // Sử dụng DatabaseMigrations đê chạy lại database sau các bài test
    use DatabaseMigrations;

    /**
     * A Dusk test LoginFail.
     * Case test login lỗi tài khoản hoặc mật khẩu không chính xác
     *
     * @return void
     */
    public function testUserLoginFailOnLoginPage()
    {
        //Khởi tạo browse test
        $this->browse(function (Browser $browser) {
            // visit đi tới màn hình login trên trình duyệt
            $browser->visit('/login')
                    // assertSee khẳng định thấy text "Login" trên trình duyệt
                    ->assertSee(__('Login'))
                    // Nhập giá trị cho input
                    ->type('email', 'test@gmail.com')
                    ->type('password', 'password')
                    // Nhấn vào button có text "Login"
                    ->press('Login')
                    //Khẳng định rằng đường dẫn hiện tại khớp với đường dẫn đã cho
                    ->assertPathIs('/login')
                    // assertSee khẳng định thấy text __('auth.failed') trên trình duyệt
                    ->assertSee(__('auth.failed'));
        });
    }

    /**
     * A Dusk test Login.
     * Case test login thành công
     *
     * @return void
     */
    public function testUserCanLoginOnLoginPage()
    {
        // Tạo dữ liệu User sử dụng để login
        $user = User::factory()->create([
            'email' => 'taylor@laravel.com',
            'password' => Hash::make('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->assertSee(__('Login'))
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home');
        });
    }
}
