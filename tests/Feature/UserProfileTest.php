<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

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
            $table->string('phone_number', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('blok_rumah', 100)->nullable();
            $table->char('rt', 2)->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->string('household_name', 100)->nullable();
            $table->unsignedInteger('role_id');
            $table->timestamps();
        });

        Role::create(['role_name' => 'Warga']);
        Role::create(['role_name' => 'Admin']);
    }

    protected function tearDown(): void
    {
        foreach (['users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_profile_shows_authenticated_users_database_details_and_functional_menu_routes(): void
    {
        $user = $this->createUser('profile-warga', [
            'full_name' => 'Adji Pratama',
            'phone_number' => '081234562819',
            'email' => 'adji@example.com',
            'household_name' => 'Keluarga Adjie',
            'address' => 'Jl. Melati No. 12, Jakarta',
            'rt' => '03',
        ]);

        $profileResponse = $this->actingAs($user)
            ->get(route('user.profile.show'))
            ->assertOk()
            ->assertSee('Profile')
            ->assertSee('Adji Pratama')
            ->assertSee('Pengguna E-Sabilillah')
            ->assertSee('0812 •••• 2819')
            ->assertSee('adji@example.com')
            ->assertSee('USR-00001')
            ->assertSee('Keluarga Adjie')
            ->assertSee('Jl. Melati No. 12, Jakarta')
            ->assertSee(route('user.profile.edit'))
            ->assertSee(route('user.settings'))
            ->assertSee(route('user.help'))
            ->assertSee(route('logout'))
            ->assertSee(asset('icons/Edittable-icon.png'))
            ->assertSee(asset('icons/settings-icon.png'))
            ->assertSee(asset('icons/Bantuan-icon.png'))
            ->assertSee(asset('icons/logout-icon.png'));

        self::assertSame(
            3,
            substr_count($profileResponse->getContent(), 'rotate-180 object-contain')
        );
        self::assertStringNotContainsString('>→</span>', $profileResponse->getContent());

        $this->actingAs($user)
            ->get(route('user.profile.edit'))
            ->assertOk()
            ->assertSee('Edit Profile')
            ->assertSee('Informasi akun')
            ->assertSee('Informasi rumah tangga')
            ->assertSee('value="Adji Pratama"', false)
            ->assertSee('value="081234562819"', false)
            ->assertSee('value="adji@example.com"', false)
            ->assertSee('value="Keluarga Adjie"', false)
            ->assertSee('value="03"', false)
            ->assertSee('name="rt"', false)
            ->assertSee('Beberapa data mungkin perlu dikonfirmasi petugas sebelum diubah.')
            ->assertSee('Simpan Perubahan');
        $this->actingAs($user)
            ->get(route('user.settings'))
            ->assertOk()
            ->assertSee('Pengaturan Akun')
            ->assertSee('Adji Pratama')
            ->assertSee('adji@example.com')
            ->assertSee('0812 •••• 2819')
            ->assertSee(route('password.request'))
            ->assertSee(route('user.profile.edit').'#phone_number')
            ->assertSee(route('user.profile.edit').'#email')
            ->assertSee(route('logout'))
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Preferensi notifikasi belum tersedia.')
            ->assertSee('Kebijakan privasi')
            ->assertSee('Syarat dan ketentuan')
            ->assertSee('Belum tersedia')
            ->assertDontSee(route('user.settings').'/privacy')
            ->assertDontSee(route('user.settings').'/terms');
        $helpResponse = $this->actingAs($user)
            ->get(route('user.help'))
            ->assertOk()
            ->assertSee(asset('images/LogoE-Sabililah.png'))
            ->assertSee(route('user.profile.show'))
            ->assertSee('Ada yang bisa kami bantu?')
            ->assertSee('Pertanyaan umum')
            ->assertSee('Bagaimana cara menjual sampah?')
            ->assertSee('Di mana saya melihat status pengajuan?')
            ->assertSee('Bagaimana cara mencairkan dana?')
            ->assertSee('Bagaimana cara mengubah data profil?')
            ->assertSee('Butuh bantuan terkait pengajuan?')
            ->assertSee('E-Sabillilah · Pilah sampah, jaga lingkungan.')
            ->assertSee('duration-300 ease-out')
            ->assertSee('data-faq-toggle', false);
        $helpContent = $helpResponse->getContent();

        self::assertSame(0, substr_count($helpContent, 'aria-expanded="true"'));
        self::assertSame(4, substr_count($helpContent, 'aria-expanded="false"'));
    }

    public function test_warga_can_update_only_their_own_profile_data(): void
    {
        $user = $this->createUser('profile-owner', [
            'full_name' => 'Nama Lama',
            'email' => 'old@example.com',
            'household_name' => 'Rumah Lama',
            'blok_rumah' => 'A-12',
            'rt' => '03',
        ]);
        $otherUser = $this->createUser('profile-other', [
            'full_name' => 'Warga Lain',
            'email' => 'other@example.com',
        ]);

        $this->actingAs($user)
            ->put(route('user.profile.update'), [
                'full_name' => 'Nama Baru',
                'phone_number' => '081234567890',
                'email' => 'new@example.com',
                'household_name' => 'Rumah Baru',
                'address' => 'Alamat Baru',
                'rt' => '03',
            ])
            ->assertRedirect(route('user.profile.show'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'full_name' => 'Nama Baru',
            'phone_number' => '081234567890',
            'email' => 'new@example.com',
            'household_name' => 'Rumah Baru',
            'address' => 'Alamat Baru',
            'rt' => '03',
            'blok_rumah' => 'A-12',
        ]);
        $this->assertDatabaseHas('users', [
            'user_id' => $otherUser->user_id,
            'full_name' => 'Warga Lain',
            'email' => 'other@example.com',
        ]);
    }

    public function test_profile_edit_validates_fields_and_profile_routes_require_warga(): void
    {
        $warga = $this->createUser('profile-invalid');

        $this->actingAs($warga)
            ->from(route('user.profile.edit'))
            ->put(route('user.profile.update'), [
                'full_name' => '',
                'phone_number' => 'not-a-phone',
                'email' => 'not-an-email',
                'rt' => '3A',
            ])
            ->assertRedirect(route('user.profile.edit'))
            ->assertSessionHasErrors(['full_name', 'phone_number', 'email', 'rt']);

        $admin = $this->createUser('profile-admin', [], 'Admin');

        $this->actingAs($admin)
            ->get(route('user.profile.show'))
            ->assertForbidden();

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->get(route('user.profile.show'))
            ->assertRedirect(route('login'));
    }

    public function test_profile_logout_uses_existing_logout_route(): void
    {
        $user = $this->createUser('profile-logout');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    private function createUser(string $username, array $attributes = [], string $roleName = 'Warga'): User
    {
        return User::create(array_merge([
            'username' => $username,
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', $roleName)->value('role_id'),
        ], $attributes));
    }
}
