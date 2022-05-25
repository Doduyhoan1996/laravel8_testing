<?php

/**
 * Test chức năng post trên trình duyệt
 */

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

    /**
     * Khởi tạo các thuộc tính sẽ sử dụng nhiều khi trước khi bắt đầu test
     */
    public function setUp(): void {
        // gọi lại setUp của parent
        parent::setUp();

        // Tạo dữ liệu User
        $this->user = User::factory()->create();

        // Tạo dữ liệu Post
        $this->post = Post::create([
            'post' => 'Post fake',
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test Case tạo mới post trên trình duyệt
     * @return void
     */
    public function testUserCanCreatePostInPage()
    {
        $this->browse(function ($browser) {
            $browser
                // Login với user đã được tạo
                ->loginAs($this->user)
                ->visit(route('post.create'))
                ->assertSee(__('Add'))
                ->type('post', 'Test add post')
                // attach file vào input
                ->attach('image', __DIR__.'/images/test.jpg')
                ->press(__('Add'))
                ->assertPathIs('/post')
                ->assertSee(__('Create success'))
                ->assertSee($this->user->name)
                ->assertSee($this->user->email)
                ->assertSee('Test add post');
        });
    }

    /**
     * Test Case sửa post trên trình duyệt
     * @return void
     */
    public function testUserCanUpdatePostInPage()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(route('post.index'))
                ->assertSee(__('List Post'))
                // Sử lý thao tác với các element trên table
                ->with('table', function ($table) {
                    $table
                        // assertSee khẳng định thấy id của post trên table
                        ->assertSee($this->post->id)
                        // assertSee khẳng định thấy post trên table
                        ->assertSee($this->post->post)
                        // click vào link với text __('Edit') trên table
                        ->clickLink(__('Edit'));
                })
                // assertUrlIs khẳng định đường dẫn hiện tại là:
                ->assertUrlIs(route('post.edit', $this->post->id))
                // Nhập giá trị cho input
                ->type('post', 'Post edit')
                ->press(__('Edit'))
                ->assertPathIs('/post')
                ->assertSee(__('Update success'))
                ->assertSee('Post edit');
        });

    }

    /**
     * Test Case xóa post trên trình duyệt
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
                        // assertSee khẳng định thấy id của post trên table
                        ->assertSee($this->post->id)
                        // assertSee khẳng định thấy post trên table
                        ->assertSee($this->post->post)
                        ->clickLink(__('Delete'))
                        // khẳng định thấy Dialog mở ra với text "Delete This post?"
                        ->assertDialogOpened('Delete This post?')
                        // Chấp nhận nút "OK" trên Dialog
                        ->acceptDialog('OK');
                })
                ->assertPathIs('/post')
                // assertSee khẳng định thấy text __('Delete success')
                ->assertSee(__('Delete success'))
                // assertDontSee khẳng định không thấy id của post trên trình duyệt
                ->assertDontSee($this->post->id)
                // assertDontSee khẳng định không thấy post trên trình duyệt
                ->assertDontSee($this->post->post);
        });
    }

}
