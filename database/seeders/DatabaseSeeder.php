<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        DB::table('posts')->insert([
            [
                'post' => 'Test post 1',
                'user_id' =>  $user->id,
            ],
            [
                'post' => 'Test post 2',
                'user_id' =>  $user->id,
            ]
        ]);
    }
}
