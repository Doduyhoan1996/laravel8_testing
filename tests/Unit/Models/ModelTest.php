<?php

/**
 * Test ModelTest
 */

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    // Test Relationship HasMany
    public function testUserHasManyPosts()
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(HasMany::class, $user->posts());
        $this->assertEquals('user_id', $user->posts()->getForeignKeyName());
    }

    // Test Relationship BeLongsTo
    public function testPostBeLongsToUser() {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(BelongsTo::class, $post->user());
        $this->assertEquals('user_id', $post->user()->getForeignKeyName());
    }
}
