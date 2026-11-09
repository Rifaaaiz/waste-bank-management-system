<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->increments('user_id');
            $table->string('username', 50)->unique();
            $table->string('password_hash');
            $table->string('email', 100)->nullable()->unique();
            $table->string('full_name', 100)->nullable();
            $table->string('family_card_number', 50)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('address')->nullable();
            $table->char('rt', 2)->nullable();
            $table->unsignedInteger('role_id');
            $table->string('profile_photo')->nullable();
            $table->string('household_name', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_forgot_password_page_uses_the_public_image_and_email_request_route(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('E-Sabilillah')
            ->assertSee(asset('images/cewe2.png'))
            ->assertSee(route('password.email'))
            ->assertSee('Masukan Email Terdaftar');
    }

    public function test_reset_request_sends_link_only_when_username_and_email_match(): void
    {
        $user = $this->createUser();
        Notification::fake();

        $response = $this->from(route('password.request'))
            ->post(route('password.email'), [
                'username' => $user->username,
                'email' => $user->email,
            ]);

        $response->assertRedirect(route('password.request'))
            ->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);

        Notification::fake();

        $this->post(route('password.email'), [
            'username' => $user->username,
            'email' => 'different@example.com',
        ])->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_valid_email_token_resets_password_using_the_password_hash_column(): void
    {
        $user = $this->createUser();
        $token = Password::broker()->createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->assertOk()
            ->assertSee('Password Baru')
            ->assertSee('Konfirmasi Password');

        $this->post(route('password.update'), [
            'username' => $user->username,
            'email' => $user->email,
            'token' => $token,
            'password' => 'A-new-secure-password',
            'password_confirmation' => 'A-new-secure-password',
        ])->assertRedirect(route('password.request'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('A-new-secure-password', $user->fresh()->password_hash));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    private function createUser(): User
    {
        return User::create([
            'username' => 'test-user',
            'password_hash' => 'Old-password-1',
            'email' => 'user@example.com',
            'role_id' => 1,
        ]);
    }
}
