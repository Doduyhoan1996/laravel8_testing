<?php

/**
 * Test View Post
 */

namespace Tests\Unit\Views;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class postViewTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @var \App\Models\Post
    */
    protected $post;

    public function setUp(): void {
        parent::setUp();

        $this->post = Post::factory()->create([
            'post' => 'post test view'
        ]);
    }

    // Test view Post Index
    public function testViewPostIndex()
    {
        $posts = Post::paginate();
        $view = $this->view('post.index', [
            'posts' => $posts
        ]);
        $view->assertSee(__('List Post'));
        $view->assertSeeTextInOrder([
            $this->post->id,
            $this->post->name,
            $this->post->email,
            $this->post->post
        ]);
    }

    // Test view Post Edit
    public function testViewPostEdit()
    {
        $view = $this->withViewErrors([])->view('post.edit', [
            'post' => $this->post
        ]);
        $view->assertSee(__('List Post'));
        $view->assertSeeText($this->post->post);
        $view->assertSee(__('Edit'));
    }
}
