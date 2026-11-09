<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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

        Role::create(['role_name' => 'Admin']);
        Role::create(['role_name' => 'Warga']);
    }

    public function test_admin_login_page_uses_organization_logo_and_admin_post_route(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Login Untuk Mengakses akun Admin')
            ->assertSee(asset('images/LogoE-Sabililah.png'))
            ->assertSee(route('admin.login.process'))
            ->assertSee(route('admin.password.request'))
            ->assertDontSee('name="remember"', false);
    }

    public function test_admin_can_login_access_dashboard_and_logout(): void
    {
        $admin = $this->createUser('administrator', 'Admin');

        $this->post(route('admin.login.process'), [
            'username' => $admin->username,
            'password' => 'Strong-password-123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('administrator');

        $this->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_non_admin_cannot_login_to_admin_or_access_dashboard(): void
    {
        $member = $this->createUser('member', 'Warga');

        $this->from(route('admin.login'))
            ->post(route('admin.login.process'), [
                'username' => $member->username,
                'password' => 'Strong-password-123',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('username');

        $this->actingAs($member)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_admin_login_for_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    private function createUser(string $username, string $roleName): User
    {
        return User::create([
            'username' => $username,
            'password_hash' => Hash::make('Strong-password-123'),
            'role_id' => Role::where('role_name', $roleName)->value('role_id'),
        ]);
    }
}
