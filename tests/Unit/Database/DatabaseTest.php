<?php

/**
 * Test Database
 */
namespace Tests\Unit\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    // Test case bảng user không có dữ liệu
    public function testUsersTableMissingData()
    {
        $this->assertDatabaseMissing('users', [
            'name' => 'Admin'
        ]);
        $this->assertDatabaseCount('users', 0);
    }

    // Test case bảng user có dữ liệu sau khi chạy seed
    public function testUsersTableHasDataIfSeedersWorks()
    {
        // Run UsersTableSeeder seeder...
        $this->seed(UsersTableSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'is_admin' => true,
        ]);
        $this->assertDatabaseCount('users', 1);
    }

    // Test case bảng post không có dữ liệu
    public function testPostsTableMissingData()
    {
        $this->assertDatabaseCount('posts', 0);
    }

    // Test case bảng post có dữ liệu sau khi chạy seed
    public function testPostsTableHasDataIfSeedersWorks()
    {
        // Run the DatabaseSeeder...
        $this->seed();
        $this->assertDatabaseCount('posts', 2);
        $post = Post::first();
        $this->assertInstanceOf(User::class, $post->user);
    }

}
