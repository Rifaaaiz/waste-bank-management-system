<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminPasswordResetNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');

        Schema::create('roles', function (Blueprint $table): void {
            $table->increments('role_id');
            $table->string('role_name', 50)->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->increments('user_id');
            $table->string('username', 50)->unique();
            $table->string('password_hash');
            $table->string('email', 100)->nullable()->unique();
            $table->string('full_name', 100)->nullable();
            $table->string('family_card_number', 50)->nullable()->unique();
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

        Role::create(['role_name' => 'Admin']);
        Role::create(['role_name' => 'Warga']);
    }

    public function test_admin_login_links_to_reset_page_and_reset_form_uses_organization_logo(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee(route('admin.password.request'));

        $this->get(route('admin.password.request'))
            ->assertOk()
            ->assertSee('Reset Password')
            ->assertSee(asset('images/LogoE-Sabililah.png'))
            ->assertSee(route('admin.password.email'))
            ->assertSee('name="username"', false)
            ->assertSee('name="email"', false);
    }

    public function test_reset_link_is_sent_only_for_an_admin_and_uses_admin_reset_route(): void
    {
        $admin = $this->createUser('administrator', 'Admin');
        $member = $this->createUser('member', 'Warga');
        Notification::fake();

        $this->from(route('admin.password.request'))
            ->post(route('admin.password.email'), [
                'username' => $admin->username,
                'email' => $admin->email,
            ])
            ->assertRedirect(route('admin.password.request'))
            ->assertSessionHas('status');

        Notification::assertSentTo($admin, AdminPasswordResetNotification::class);
        Notification::assertNotSentTo($member, AdminPasswordResetNotification::class);

        $this->post(route('admin.password.email'), [
            'username' => $member->username,
            'email' => $member->email,
        ])->assertSessionHas('status');

        Notification::assertNotSentTo($member, AdminPasswordResetNotification::class);

        $token = Password::broker()->createToken($admin);
        $notification = new AdminPasswordResetNotification($token);
        $resetUrl = $notification->toMail($admin)->actionUrl;

        $this->assertStringContainsString(route('admin.password.reset', [
            'token' => $token,
            'email' => $admin->email,
        ]), $resetUrl);
    }

    public function test_valid_admin_token_resets_hashed_password_and_can_be_used_to_login(): void
    {
        $admin = $this->createUser('administrator', 'Admin');
        $token = Password::broker()->createToken($admin);

        $this->get(route('admin.password.reset', [
            'token' => $token,
            'email' => $admin->email,
        ]))
            ->assertOk()
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('name="token"', false);

        $this->post(route('admin.password.update'), [
            'username' => $admin->username,
            'email' => $admin->email,
            'token' => $token,
            'password' => 'A-new-admin-password',
            'password_confirmation' => 'A-new-admin-password',
        ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('A-new-admin-password', $admin->fresh()->password_hash));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $admin->email]);

        $this->post(route('admin.login.process'), [
            'username' => $admin->username,
            'password' => 'A-new-admin-password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_non_admin_and_invalid_or_reused_tokens_cannot_reset_password(): void
    {
        $member = $this->createUser('member', 'Warga');
        $oldPasswordHash = $member->password_hash;
        $token = Password::broker()->createToken($member);

        $this->get(route('admin.password.reset', [
            'token' => $token,
            'email' => $member->email,
        ]))
            ->assertRedirect(route('admin.password.request'))
            ->assertSessionHasErrors('email');

        $this->post(route('admin.password.update'), [
            'username' => $member->username,
            'email' => $member->email,
            'token' => $token,
            'password' => 'A-new-admin-password',
            'password_confirmation' => 'A-new-admin-password',
        ])->assertSessionHasErrors('email');

        $this->assertSame($oldPasswordHash, $member->fresh()->password_hash);
    }

    private function createUser(string $username, string $roleName): User
    {
        return User::create([
            'username' => $username,
            'email' => "{$username}@example.com",
            'password_hash' => Hash::make('Old-password-123'),
            'role_id' => Role::where('role_name', $roleName)->value('role_id'),
        ]);
    }
}
