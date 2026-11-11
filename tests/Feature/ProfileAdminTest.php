<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAdminTest extends TestCase
{
    protected User $admin;

    protected User $otherAdmin;

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
        $roleId = Role::query()->where('role_name', 'Admin')->value('role_id');

        $this->admin = User::create([
            'username' => 'admin-profile',
            'password_hash' => Hash::make('Current-password-123'),
            'full_name' => 'Admin Profile',
            'phone_number' => '08123456789',
            'address' => 'Jalan Melati 10',
            'position' => 'Administrator',
            'role_id' => $roleId,
        ]);
        $this->otherAdmin = User::create([
            'username' => 'admin-other',
            'password_hash' => Hash::make('Other-password-123'),
            'full_name' => 'Admin Lain',
            'phone_number' => '08987654321',
            'address' => 'Alamat lain',
            'role_id' => $roleId,
        ]);
    }

    public function test_profile_shows_only_authenticated_admin_data_and_hides_password_hash(): void
    {
        $hash = $this->admin->password_hash;

        $this->actingAs($this->admin)
            ->get(route('admin.profile.show'))
            ->assertOk()
            ->assertSee('Admin Profile')
            ->assertSee('Administrator')
            ->assertSee('08123456789')
            ->assertSee('Jalan Melati 10')
            ->assertSee(asset('images/oranghitam.png'))
            ->assertSee('<a class="admin-nav-link active" href="'.route('admin.profile.show').'"', false)
            ->assertDontSee('<a class="admin-nav-link active" href="'.route('admin.transaksi.index').'"', false)
            ->assertDontSee('<a class="admin-subnav-item active"', false)
            ->assertDontSee($hash)
            ->assertDontSee('Admin Lain')
            ->assertDontSee('Other-password-123');
    }

    public function test_admin_can_update_own_profile_and_upload_a_photo_without_changing_role(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->put(route('admin.profile.update'), [
                'full_name' => 'Nama Admin Baru',
                'profile_photo' => UploadedFile::fake()->create('admin.svg', 100, 'image/svg+xml'),
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('profile_photo');

        $this->assertSame('Admin Profile', $this->admin->fresh()->full_name);

        $this->actingAs($this->admin)
            ->put(route('admin.profile.update'), [
                'full_name' => 'Nama Admin Baru',
                'phone_number' => '+628123456789',
                'address' => 'Alamat Admin Baru',
                'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
                'profile_photo' => UploadedFile::fake()->image('admin.png'),
            ])
            ->assertRedirect(route('admin.profile.show'))
            ->assertSessionHas('status');

        $this->admin->refresh();
        $this->assertSame('Nama Admin Baru', $this->admin->full_name);
        $this->assertSame('+628123456789', $this->admin->phone_number);
        $this->assertSame('Alamat Admin Baru', $this->admin->address);
        $this->assertSame('Admin', $this->admin->role->role_name);
        $this->assertNotNull($this->admin->profile_photo);
        $this->assertTrue(Storage::disk('public')->exists($this->admin->profile_photo));
        $this->assertSame('Admin Lain', $this->otherAdmin->fresh()->full_name);

        $this->actingAs($this->admin)
            ->get(route('admin.profile.show'))
            ->assertOk()
            ->assertSee(route('admin.profile.photo'));

        $this->actingAs($this->admin)
            ->get(route('admin.profile.photo'))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_admin_must_verify_current_password_and_confirm_new_password(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.profile.password'), [
                'current_password' => 'incorrect',
                'password' => 'New-password-123',
                'password_confirmation' => 'New-password-123',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('Current-password-123', $this->admin->fresh()->password_hash));

        $this->actingAs($this->admin)
            ->put(route('admin.profile.password'), [
                'current_password' => 'Current-password-123',
                'password' => 'New-password-123',
                'password_confirmation' => 'Different-password-123',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('Current-password-123', $this->admin->fresh()->password_hash));

        $this->actingAs($this->admin)
            ->put(route('admin.profile.password'), [
                'current_password' => 'Current-password-123',
                'password' => 'New-password-123',
                'password_confirmation' => 'New-password-123',
            ])
            ->assertRedirect(route('admin.profile.show'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('New-password-123', $this->admin->fresh()->password_hash));
        $this->assertNotSame('New-password-123', $this->admin->fresh()->password_hash);
        $this->assertTrue(Hash::check('Other-password-123', $this->otherAdmin->fresh()->password_hash));
    }

    public function test_guests_and_non_admin_users_cannot_access_admin_profile(): void
    {
        $this->get(route('admin.profile.show'))
            ->assertRedirect(route('admin.login'));

        $citizen = User::create([
            'username' => 'citizen-profile',
            'password_hash' => Hash::make('Citizen-password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
        ]);

        $this->actingAs($citizen)
            ->get(route('admin.profile.show'))
            ->assertForbidden();

        $this->actingAs($citizen)
            ->put(route('admin.profile.update'), [
                'full_name' => 'Tidak boleh',
            ])
            ->assertForbidden();
    }
}
