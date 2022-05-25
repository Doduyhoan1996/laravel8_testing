<?php

/**
 * Test tính năng PasswordConfirmation
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @var \App\Models\User
    */
    protected $user;

    public function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    // test case có thể render view Confirm Password thông qua HTTP request
    public function testConfirmPasswordScreenCanBeRendered()
    {
        $response = $this->actingAs($this->user)->get('/password/confirm');

        $response->assertStatus(200);
    }

    // test case có thể Confirm Password thông qua HTTP request
    public function testPasswordCanBeConfirmed()
    {
        $response = $this->actingAs($this->user)->post('/password/confirm', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    // test case Confirm Password thông qua HTTP request
    public function testPasswordIsNotConfirmedWithInvalidPassword()
    {
        $response = $this->actingAs($this->user)->post('/password/confirm', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
