<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Post;

class PostTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
    * @var \App\Models\User
    */
    protected $user;

     /**
    * @var \App\Models\Post
    */
    protected $post;

    public function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->post = Post::create([
            'post' => 'Post fake',
            'user_id' => $this->user->id
        ]);
    }

    /**
     *
     * @return void
     */
    public function testUserCanCreatePostInPage()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(route('post.create'))
                ->assertSee(__('Add'))
                ->type('post', 'Test add post')
                // ->attach('image', __DIR__.'/images/test.jpg')
                ->press(__('Add'))
                ->assertPathIs('/post')
                ->assertSee(__('Create success'))
                ->assertSee($this->user->name)
                ->assertSee($this->user->email)
                ->assertSee('Test add post');
        });
    }

    /**
     *
     * @return void
     */
    public function testUserCanUpdatePostInPage()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(route('post.index'))
                ->assertSee(__('List Post'))
                ->with('table', function ($table) {
                    $table
                        ->assertSee($this->post->id)
                        ->assertSee($this->post->post)
                        ->clickLink(__('Edit'));
                })->assertUrlIs(route('post.edit', $this->post->id))
                ->type('post', 'Post edit')
                ->press(__('Edit'))
                ->assertPathIs('/post')
                ->assertSee(__('Update success'))
                ->assertSee('Post edit');
        });

    }

    /**
     *
     * @return void
     */
    public function testUserCanDeletePostInPage()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(route('post.index'))
                ->assertSee(__('List Post'))
                ->with('table', function ($table) {
                    $table
                        ->assertSee($this->post->id)
                        ->assertSee($this->post->post)
                        ->clickLink(__('Delete'))
                        ->assertDialogOpened('Delete This post?')
                        ->acceptDialog(__('OK'));
                })
                ->assertPathIs('/post')
                ->assertSee(__('Delete success'))
                ->assertDontSee($this->post->id)
                ->assertDontSee($this->post->post);
        });
    }

}
