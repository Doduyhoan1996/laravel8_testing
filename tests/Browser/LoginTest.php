<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test Login.
     *
     * @return void
     */
    public function testUserLoginFailOnLoginPage()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                    ->assertSee(__('Login'))
                    ->type('email', 'test@gmail.com')
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/login')
                    ->assertSee(__('auth.failed'));
        });
    }

    /**
     * A Dusk test Login.
     *
     * @return void
     */
    public function testUserCanLoginOnLoginPage()
    {
        $user = User::factory()->create([
            'email' => 'taylor@laravel.com',
            'password' => Hash::make('password'),
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                    ->assertSee(__('Login'))
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home');
        });
    }
}
