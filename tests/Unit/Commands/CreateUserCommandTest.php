<?php

namespace Tests\Unit\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserCommandTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test a console command.
     *
     * @return void
     */
    public function testCommandCreateUserSuccess()
    {
        $input = [
            'name' => 'Test Name',
            'email' => 'command@gmail.com',
            'password' => 12345678,
            'is_admin' => 'true',
        ];

        $this->artisan('command:create-user')
            ->expectsQuestion('Enter name', $input['name'])
            ->expectsQuestion('Enter email', $input['email'])
            ->expectsQuestion('Enter password', $input['password'])
            ->expectsQuestion('Choice is Admin', $input['is_admin'])
            ->expectsOutput('Welcome '. $input['name'] .'. Your account created.')
            ->doesntExpectOutput('Welcome Test. Your account created.')
            ->assertExitCode(1);

        $user = User::first();
        $this->assertEquals($input['name'], $user->name);
        $this->assertEquals($input['email'], $user->email);
        $this->assertEquals($input['is_admin'], $input['is_admin']);
    }

    public function testCommandCreateUserFails()
    {
        $input = [
            'name' => null,
            'email' => null,
            'password' => null,
            'is_admin' => 'false',
        ];

        $this->artisan('command:create-user')
            ->expectsQuestion('Enter name', $input['name'])
            ->expectsQuestion('Enter email', $input['email'])
            ->expectsQuestion('Enter password', $input['password'])
            ->expectsQuestion('Choice is Admin', $input['is_admin'])
            ->doesntExpectOutput('Welcome '. $input['name'] .'. Your account created.')
            ->expectsOutput('User not created.')
            ->assertExitCode(0)
            ->assertFailed();

    }
}
