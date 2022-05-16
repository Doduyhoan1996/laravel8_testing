<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @var \App\Models\User
    */
    protected $user;

    /**
    * @var \App\Models\User
    */
    protected $other_user;

    /**
    * @var \App\Models\Post
    */
    protected $post;

    public function setUp(): void {
        parent::setUp();
        //setUp create User
        $this->user = User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $this->other_user = User::factory()->create([
            'email' => 'otheruser@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $this->post = Post::create([
            'post' => 'post fake',
            'user_id' => $this->other_user->id
        ]);
    }

    public function testCanNotAccessPostIndexPageWithoutAuthenticate()
    {
        $response = $this->get('post');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testPostsIndexPageIsRendered()
    {
        //login guard with $user
        $this->actingAs($this->user);
        $response = $this->get('post');
        $response->assertStatus(200)
            ->assertSee('post fake');
    }

    public function testUserCanCreatePost()
    {
        $this->actingAs($this->user);

        Storage::fake('public');
        $fake_image = UploadedFile::fake()->image('post_image.jpg');

        $response = $this->post('/post/store', [
            'post' => 'test post create',
            'image' => $fake_image,
        ]);

        $post = Post::where('user_id', $this->user->id)->first();

        $this->assertEquals($this->user->id, $post->user_id);
        $this->assertEquals('test post create', $post->post);
        $this->assertInstanceOf(User::class, $post->user);
        Storage::disk('public')->assertExists( Post::IMAGE_FOLDER .'/' . $post->image);

        $response->assertStatus(302)
            ->assertRedirect('/post')
            ->assertSessionHas('success', __('Create success'));
    }

    public function testUserCanEditPost()
    {
        $this->actingAs($this->other_user);
        $response = $this->get('post/edit/' . $this->post->id);
        $response->assertStatus(200)
            ->assertSee('post fake');
    }

    public function testUserCanUpdateMyPost()
    {
        $this->actingAs($this->other_user);

        Storage::fake('public');
        $fake_image = UploadedFile::fake()->image('image_update.jpg');

        $response = $this->post('/post/update/'. $this->post->id, [
            'post' => 'test post update',
            'image' => $fake_image,
        ]);

        $post = Post::find($this->post->id);

        $this->assertEquals($this->other_user->id, $post->user_id);
        $this->assertEquals('test post update', $post->post);
        $this->assertNotEquals($this->post->image, $post->image);
        $this->assertInstanceOf(User::class, $post->user);
        Storage::disk('public')->assertExists( Post::IMAGE_FOLDER .'/' . $post->image);

        $response->assertStatus(302)
            ->assertRedirect('/post')
            ->assertSessionHas('success', __('Update success'));
    }

    public function testUserCanNotEditOtherPost()
    {
        $this->actingAs($this->user);
        $response = $this->get('post/edit/' . $this->post->id);
        $response->assertStatus(403);
    }

    public function testUserCanNotUpdateOtherPost()
    {
        $this->actingAs($this->user);
        $response = $this->post('/post/update/'. $this->post->id, [
            'post' => 'test post other update',
        ]);
        $response->assertStatus(403);
    }

    public function testUserCanNotDestroyOtherPost()
    {
        $this->actingAs($this->user);
        $response = $this->get('/post/destroy/'. $this->post->id);
        $response->assertStatus(403);
    }

    public function testUserCanDestroyOtherPost()
    {
        $this->actingAs($this->other_user);
        $response = $this->get('/post/destroy/'. $this->post->id);
        $response->assertStatus(302)
            ->assertRedirect('/post')
            ->assertSessionHas('success', __('Delete success'));
    }
}
