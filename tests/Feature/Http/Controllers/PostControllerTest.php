<?php

/**
 * Test Controller PostController
 */

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Faker\Factory as Faker;

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
        $this->faker = Faker::create();

        //tạo Storage fake
        Storage::fake('public');
        //setUp create User
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->other_user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->post = Post::create([
            'post' => 'post fake',
            'user_id' => $this->other_user->id
        ]);
    }

    // test case không thể truy cập post index khi chưa đăng nhập
    public function testCanNotAccessPostIndexPageWithoutAuthenticate()
    {
        $response = $this->get('post');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    // test case có thể truy cập post index khi đã đăng nhập
    public function testPostsIndexPageIsRendered()
    {
        //login guard with $user
        $this->actingAs($this->user);
        $response = $this->get('post');
        $response->assertStatus(200)
            //assertSee thấy text "post fake" là post đã được tạo ở setUp()
            ->assertSee('post fake');
    }

    // test case có thể tạo post
    public function testUserCanCreatePost()
    {
        $this->actingAs($this->user);

        // Tạo fake image
        $fake_image = UploadedFile::fake()->image('post_image.jpg');

        $response = $this->post('/post/store', [
            'post' => 'test post create',
            'image' => $fake_image,
        ]);

        // Tìm kiếm post vừa được tạo
        $post = Post::where('user_id', $this->user->id)->first();

        // khẳng định thông tin post vừa tạo giống với trong CSDL
        $this->assertEquals($this->user->id, $post->user_id);
        $this->assertEquals('test post create', $post->post);

        // khẳng định $post->user có đúng là Instance của User::class
        $this->assertInstanceOf(User::class, $post->user);

        // khẳng định image đã được lưu vào Storage
        Storage::disk('public')->assertExists( Post::IMAGE_FOLDER .'/' . $post->image);

        $response->assertStatus(302)
            ->assertRedirect('/post')
            ->assertSessionHas('success', __('Create success'));
    }

    // test case user không thể tạo post
    public function testUserCanNotCreatePost()
    {
        $this->actingAs($this->user);

        // Tạo fake image với size làm 3000 kilobytes
        $fake_image = UploadedFile::fake()->image('post_image.jpg')->size(3000);

        $response = $this->post('/post/store', [
            'post' => null,
            'image' => $fake_image,
        ]);

        $response->assertStatus(302)
            //khẳng định tồn tại message lỗi được trả về khi nhập sai thông tin
            ->assertSessionHasErrors([
                'post' => 'The post field is required.',
                'image' => 'The image may not be greater than 2048 kilobytes.',
            ]);
    }

    // test case user có thể truy cập post edit khi đã đăng nhập
    public function testUserCanEditPost()
    {
        $this->actingAs($this->other_user);
        $response = $this->get('post/edit/' . $this->post->id);
        $response->assertStatus(200)
            ->assertSee('post fake');
    }

    // test case user có sửa post do mình tạo
    public function testUserCanUpdateMyPost()
    {
        $this->actingAs($this->other_user);

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

    // test case user không thể truy cập post edit của người khác
    public function testUserCanNotEditOtherPost()
    {
        $this->actingAs($this->user);
        $response = $this->get('post/edit/' . $this->post->id);
        $response->assertStatus(403);
    }

    // test case user không thể sửa post của người khác
    public function testUserCanNotUpdateOtherPost()
    {
        $this->actingAs($this->user);
        $response = $this->post('/post/update/'. $this->post->id, [
            'post' => $this->faker->paragraph(),
        ]);
        $response->assertStatus(403);
    }

    // test case user không thể xóa post của người khác
    public function testUserCanNotDestroyOtherPost()
    {
        $this->actingAs($this->user);
        $response = $this->get('/post/destroy/'. $this->post->id);
        $response->assertStatus(403);
    }

    // test case user có thể xóa post của mình
    public function testUserCanDestroyOtherPost()
    {
        $this->actingAs($this->other_user);
        $response = $this->get('/post/destroy/'. $this->post->id);
        $response->assertStatus(302)
            ->assertRedirect('/post')
            ->assertSessionHas('success', __('Delete success'));
    }
}
