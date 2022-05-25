<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command create user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $input['name'] = $this->ask('Enter name');
        $input['email'] = $this->ask('Enter email');
        $input['password'] = $this->secret('Enter password');

        $is_admin = $this->choice('Choice is Admin', [
            'false',
            'true'
        ]);

        try {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'is_admin' => ($is_admin == 'true') ? true : false,
            ]);
            $this->info('Welcome '. $input['name'] .'. Your account created.');
            return 1;

        } catch(Exception $e) {
            $this->info('User not created.');
            return 0;
        }

    }
}
