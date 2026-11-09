<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegistrationTest extends TestCase
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

        foreach (['Admin', 'Petugas', 'Warga'] as $roleName) {
            Role::create(['role_name' => $roleName]);
        }
    }

    public function test_register_page_uses_public_images_and_expected_form_fields(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('E-Sabilillah')
            ->assertSee(asset('images/cewe2.png'))
            ->assertSee(route('login'))
            ->assertSee(route('register.store'))
            ->assertSee('name="phone_number"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="rt"', false);
    }

    public function test_registration_stores_hashed_password_and_assigns_warga_role(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), $this->registrationData())
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $user = User::where('username', 'new-warga')->firstOrFail();

        $this->assertSame('081234567890', $user->phone_number);
        $this->assertSame('Alamat rumah', $user->address);
        $this->assertSame('01', $user->rt);
        $this->assertSame('Warga', $user->role->role_name);
        $this->assertNull($user->email);
        $this->assertTrue(Hash::check('Secure-password-123', $user->password_hash));
        $this->assertNotSame('Secure-password-123', $user->password_hash);
    }

    public function test_registered_user_can_authenticate_with_username_and_password(): void
    {
        $this->post(route('register.store'), $this->registrationData());

        $user = User::where('username', 'new-warga')->firstOrFail();

        $this->post(route('login.store'), [
            'username' => 'new-warga',
            'password' => 'Secure-password-123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Auth::check());
        $this->assertAuthenticatedAs($user);
    }

    public function test_duplicate_username_and_invalid_rt_are_rejected(): void
    {
        $this->post(route('register.store'), $this->registrationData());

        $this->from(route('register'))
            ->post(route('register.store'), array_merge($this->registrationData(), [
                'phone_number' => '081234567891',
                'rt' => '1A',
            ]))
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['username', 'rt']);

        $this->assertSame(1, User::count());
    }

    private function registrationData(): array
    {
        return [
            'username' => 'new-warga',
            'phone_number' => '081234567890',
            'password' => 'Secure-password-123',
            'password_confirmation' => 'Secure-password-123',
            'address' => 'Alamat rumah',
            'rt' => '01',
        ];
    }
}
