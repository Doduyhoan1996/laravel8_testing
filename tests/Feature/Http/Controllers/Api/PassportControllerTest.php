<?php

/**
 * Test Controller PassportController
 */

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;

class PassportControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate', ['-vvv' => true]);
        Artisan::call('passport:install', ['-vvv' => true]);
        Artisan::call('db:seed', ['-vvv' => true]);
        $this->faker = Faker::create();
        //setUp create User
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    public function testUserCanRegisterOnApi()
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8)
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'success' => true,
                'message' => 'User registered succesfully, Use Login method to receive token.'
            ]);
    }

    public function testUserCanNotRegisterOnApi()
    {
        $response = $this->postJson('/api/register', [
            'name' => null,
            'email' => null,
            'password' => null
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Please see errors parameter for all errors.'
            ]);
    }

    public function testUserCanNotLoginOnApi()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->faker->email,
            'password' => 'wrong-password'
        ]);

        $response
            ->assertStatus(401)
            ->assertExactJson([
                'success' => false,
                'message' => 'User authentication failed.'
            ]);
    }

    public function testUserCanLoginOnApi()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ], [
            'Accept' => 'application/json'
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User login succesfully, Use token to authenticate.'
            ]);
    }

    public function testUserCanGetUserDetailWhenLoginOnApi()
    {
        $this->actingAs($this->user, 'api');
        $response = $this->getJson('api/user-detail');
        $response->assertOk()
            ->assertExactJson([
                'success' => true,
                'message' => 'Data fetched successfully.',
                'data' => $this->user->toArray()
            ])
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('data')
                    ->has('message')
                    ->where('success', true)
                    ->where('message', 'Data fetched successfully.')
                    ->whereType('data', 'array')
                    ->where('data.id', $this->user->id)
                    ->where('data.name', $this->user->name)
                    ->where('data.email', $this->user->email)
                    ->missing('data.password')
                    ->etc();
            });
        $this->assertEquals($this->user->toArray(), $response['data']);
    }

    public function testUserCanNotGetUserDetailOnApi()
    {
        $response = $this->getJson('api/user-detail');
        $response->assertExactJson([
            'message' => 'Unauthenticated.'
        ]);
        $this->assertGuest();
    }
}
