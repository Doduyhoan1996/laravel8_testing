<?php

/**
 * Test tính năng EmailVerification
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    // RefreshDatabase reset lại DB sau mỗi bài test
    use RefreshDatabase;

    /**
    * @var \App\Models\User
    */
    protected $user;

    public function setUp(): void {
        parent::setUp();

        // Tạo dữ liệu User
        $this->user = User::factory()->create([
            'email_verified_at' => null,
        ]);
    }

    // test case có thể render view (view thông báo tài khoản cần xác thực) thông qua HTTP request
    public function testEmailVerificationScreenCanBeRendered() {
        $response = $this->actingAs($this->user)->get('/email/verify');

        $response->assertStatus(200);
    }

    // test case có thể xác thực người dùng thông qua link gửi vào email
    public function testEmailCanBeVerified() {
        // tạo mock fake Event ( để fake event người dùng xác thực thông qua link)
        Event::fake();

        //Tạo link sử dụng để xác thực người dùng (link được gửi trong email)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );

        // thực hiện truy cập vào đường link để xác thực người dùng
        $response = $this->actingAs($this->user)->get($verificationUrl);

        // Xác nhận rằng Event Verified đã được Dispatched
        Event::assertDispatched(Verified::class);
        // Xác nhận rằng user đã được xác thực
        $this->assertTrue($this->user->fresh()->hasVerifiedEmail());
        // Xác nhận xem phản hồi có đang chuyển hướng đến màn hình home (/home)
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    // test case xác thực người dùng với mail không đúng
    public function testEmailIsNotVerifiedWithInvalidHash()
    {
        //Tạo link sử dụng để xác thực người dùng (link được gửi trong email) với email sai
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1('wrong-email')]
        );

        // thực hiện truy cập vào đường link để xác thực người dùng
        $this->actingAs($this->user)->get($verificationUrl);

        // Xác nhận rằng user không được xác thực
        $this->assertFalse($this->user->fresh()->hasVerifiedEmail());
    }
}
