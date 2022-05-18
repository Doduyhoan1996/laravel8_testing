<?php

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

    public function setUp(): void {
        parent::setUp();
        $this->user = [
            'name' => 'Register Name',
            'email' => 'test@gmail.com',
            'password' => 'password',
        ];
    }

    public function testUserRegisterFailOnRegisterPage()
    {
        $user = User::factory()->create() ;
        $this->browse(function ($browser) use ($user) {
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

    public function testUserCanRegisterOnRegisterPage()
    {
        $this->browse(function ($browser) {
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
        $this->assertEquals($this->user['name'], $user->name);
        $this->assertEquals($this->user['email'], $user->email);
    }

}
