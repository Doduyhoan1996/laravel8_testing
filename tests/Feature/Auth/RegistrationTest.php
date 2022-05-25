<?php

/**
 * Test tính năng Registration
 */

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // test case có thể render view Registration thông qua HTTP request
    public function testRegistrationScreenCanBeRendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // test case có thể Registration thông qua HTTP request
    public function testNewUsersCanRegister()
    {
        // Tạo fake dữ liệu với Faker
        $this->faker = Faker::create();
        $response = $this->post('/register', [
            'name' =>  $this->faker->name,
            'email' =>  $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
