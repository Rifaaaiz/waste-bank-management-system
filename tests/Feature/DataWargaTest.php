<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DataWargaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('accounts');
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
            $table->string('blok_rumah', 100)->nullable();
            $table->char('rt', 2)->nullable();
            $table->unsignedInteger('role_id');
            $table->string('profile_photo')->nullable();
            $table->string('household_name', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table): void {
            $table->increments('account_id');
            $table->unsignedInteger('user_id');
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });

        Role::create(['role_name' => 'Admin']);
        Role::create(['role_name' => 'Warga']);
    }

    public function test_admin_can_search_filter_and_paginate_warga_by_ten(): void
    {
        $admin = $this->createUser('admin-data', 'Admin');

        for ($number = 1; $number <= 13; $number++) {
            $this->createUser(
                sprintf('warga-%02d', $number),
                'Warga',
                sprintf('Warga %02d', $number),
                '01',
                $number <= 11 ? 'A' : 'B'
            );
        }

        $firstPage = $this->actingAs($admin)
            ->get(route('admin.warga.index'))
            ->assertOk()
            ->assertSee('Data Warga')
            ->assertSee('Search by invoice number, name, amount...')
            ->assertSee(route('admin.warga.create'))
            ->assertSee(route('admin.warga.pdf'))
            ->assertSee('<div class="admin-nav-group is-open">', false)
            ->assertSee('aria-expanded="true" aria-controls="admin-data-subnav"', false)
            ->assertSee(asset('icons/Keloladata-icon.png'))
            ->assertSee('.admin-nav-heading .admin-nav-icon', false)
            ->assertSee('transition: transform 220ms ease;', false)
            ->assertSee('transform: scale(1.08);', false)
            ->assertSee('apakah anda yakin untuk log out?')
            ->assertSee('data-logout-confirmation-open', false)
            ->assertSee('data-logout-confirmation-cancel', false)
            ->assertSee('event.target === dialog', false)
            ->assertSee('Warga 01')
            ->assertDontSee('Warga 11');

        $this->assertCount(10, $firstPage->viewData('wargas'));

        $this->get(route('admin.warga.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Warga 11')
            ->assertSee('Warga 12')
            ->assertDontSee('Warga 01');

        $this->get(route('admin.warga.index', [
            'search' => 'warga-',
            'rt' => '01',
            'blok_rumah' => 'A',
        ]))
            ->assertOk()
            ->assertSee('Warga 01')
            ->assertDontSee('Warga 12')
            ->assertSee(route('admin.warga.index', [
                'search' => 'warga-',
                'rt' => '01',
                'blok_rumah' => 'A',
                'page' => 2,
            ]));
    }

    public function test_admin_can_create_update_and_delete_warga_without_changing_role(): void
    {
        $admin = $this->createUser('admin-crud', 'Admin');
        $this->actingAs($admin);

        $this->post(route('admin.warga.store'), [
            'full_name' => 'Nama Baru',
            'username' => 'warga-baru',
            'phone_number' => '08123456789',
            'address' => 'Jalan Melati',
            'blok_rumah' => 'B-10',
            'rt' => '02',
            'password' => 'Password-aman-123',
            'password_confirmation' => 'Password-aman-123',
            'role_id' => Role::where('role_name', 'Admin')->value('role_id'),
        ])->assertRedirect(route('admin.warga.index'));

        $warga = User::where('username', 'warga-baru')->firstOrFail();
        $this->assertSame('Warga', $warga->role->role_name);
        $this->assertSame('B-10', $warga->blok_rumah);
        $this->assertTrue(Hash::check('Password-aman-123', $warga->password_hash));

        $this->get(route('admin.warga.edit', $warga->user_id))
            ->assertOk()
            ->assertSee('Edit Data Warga')
            ->assertSee('<div class="admin-nav-group is-open">', false)
            ->assertSee('Jalan Melati');

        $this->put(route('admin.warga.update', $warga->user_id), [
            'full_name' => 'Nama Diperbarui',
            'username' => 'warga-baru',
            'phone_number' => '08123456789',
            'address' => 'Jalan Mawar',
            'blok_rumah' => 'C-11',
            'rt' => '03',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.warga.index'));

        $warga->refresh();
        $this->assertSame('Nama Diperbarui', $warga->full_name);
        $this->assertSame('C-11', $warga->blok_rumah);
        $this->assertTrue(Hash::check('Password-aman-123', $warga->password_hash));

        $this->delete(route('admin.warga.destroy', $warga->user_id))
            ->assertRedirect(route('admin.warga.index'));
        $this->assertDatabaseMissing('users', ['user_id' => $warga->user_id]);
    }

    public function test_create_warga_form_shows_requested_fields_and_restricts_rt_choices(): void
    {
        $admin = $this->createUser('admin-create-form', 'Admin');

        $formResponse = $this->actingAs($admin)
            ->get(route('admin.warga.create'));

        $formResponse
            ->assertOk()
            ->assertSee('Tambah Data Warga')
            ->assertSee('Masukan Nama Warga')
            ->assertSee('Masukan Username')
            ->assertSee('Masukan Nomor Telepon')
            ->assertSee('Masukan Alamat Jalan')
            ->assertSee('Masukan Blok Rumah')
            ->assertSee('Pilih RT')
            ->assertSee('<div class="admin-nav-group is-open">', false)
            ->assertSee('aria-expanded="true" aria-controls="admin-data-subnav"', false)
            ->assertSee(asset('icons/arrow-icon.png'))
            ->assertSee('rt-dropdown-menu')
            ->assertSee('rt-dropdown-menu.open')
            ->assertSee('transform: rotate(180deg)')
            ->assertSee('grid-template-columns: repeat(2, minmax(0, 1fr))')
            ->assertSee('.back-link { display: grid')
            ->assertSee('class="secondary-button"', false)
            ->assertSeeInOrder(['class="secondary-button"', 'class="primary-button"'])
            ->assertSee('<div class="admin-nav-group is-open">', false)
            ->assertSee('transition: max-height 420ms ease-in-out', false)
            ->assertSee('max-height: 120px;', false)
            ->assertSee('admin-subnav-item active', false)
            ->assertSee('width: 220px;')
            ->assertSee('margin-left: 220px;')
            ->assertSee('width: 82px;')
            ->assertSee('margin-left: 82px;')
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('href="'.route('admin.warga.index').'"', false)
            ->assertSee('value="01"', false)
            ->assertSee('value="07"', false);

        foreach (['01', '02', '03', '04', '05', '06', '07'] as $rt) {
            $this->assertStringContainsString('value="'.$rt.'"', $formResponse->getContent());
        }

        $this->from(route('admin.warga.create'))
            ->post(route('admin.warga.store'), [
                'full_name' => 'Warga Dengan RT Tidak Valid',
                'username' => 'warga-rt-tidak-valid',
                'phone_number' => '',
                'address' => 'Jalan Melati',
                'blok_rumah' => 'B-10',
                'rt' => '03',
                'password' => 'Password-aman-123',
                'password_confirmation' => 'Password-aman-123',
            ])
            ->assertRedirect(route('admin.warga.create'))
            ->assertSessionHasErrors('phone_number')
            ->assertSessionHas('_old_input.full_name', 'Warga Dengan RT Tidak Valid');

        $this->get(route('admin.warga.create'))
            ->assertOk()
            ->assertSee('value="03" selected', false)
            ->assertSee('value="Warga Dengan RT Tidak Valid"', false);

        $this->from(route('admin.warga.create'))
            ->post(route('admin.warga.store'), [
                'full_name' => 'RT Di Luar Opsi',
                'username' => 'warga-rt-tidak-valid',
                'phone_number' => '08123456789',
                'address' => 'Jalan Melati',
                'blok_rumah' => 'B-10',
                'rt' => '08',
                'password' => 'Password-aman-123',
                'password_confirmation' => 'Password-aman-123',
            ])
            ->assertRedirect(route('admin.warga.create'))
            ->assertSessionHasErrors('rt');

        $this->assertDatabaseMissing('users', ['username' => 'warga-rt-tidak-valid']);
    }

    public function test_warga_with_bank_account_cannot_be_deleted(): void
    {
        $admin = $this->createUser('admin-delete', 'Admin');
        $warga = $this->createUser('warga-account', 'Warga');

        DB::table('accounts')->insert([
            'user_id' => $warga->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.warga.index'))
            ->delete(route('admin.warga.destroy', $warga->user_id))
            ->assertRedirect(route('admin.warga.index'))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('users', ['user_id' => $warga->user_id]);
    }

    public function test_admin_filter_handles_zero_values_and_non_admin_cannot_open_page(): void
    {
        $admin = $this->createUser('admin-zero-filter', 'Admin');
        $this->createUser('warga-zero', 'Warga', 'Warga Zero', '00', '0');
        $this->createUser('warga-zero-other', 'Warga', 'Warga Zero Other', '00', 'C');

        $this->actingAs($admin)
            ->get(route('admin.warga.index', ['rt' => '00', 'blok_rumah' => '0']))
            ->assertOk()
            ->assertSee('Warga Zero')
            ->assertDontSee('Warga Zero Other');

        $member = User::where('username', 'warga-zero')->firstOrFail();
        $this->actingAs($member)
            ->get(route('admin.warga.index'))
            ->assertForbidden();
    }

    public function test_pdf_download_uses_real_warga_records_and_filters(): void
    {
        $admin = $this->createUser('admin-pdf', 'Admin');
        $this->createUser('warga-pdf', 'Warga', 'Warga PDF', '04', 'D-4');

        $response = $this->actingAs($admin)
            ->get(route('admin.warga.pdf', ['rt' => '04']));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-data-warga.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    private function createUser(
        string $username,
        string $roleName,
        ?string $fullName = null,
        string $rt = '01',
        string $blokRumah = 'A-1'
    ): User {
        return User::create([
            'username' => $username,
            'password_hash' => Hash::make('Password-aman-123'),
            'full_name' => $fullName ?? $username,
            'phone_number' => '08123456789',
            'address' => 'Jalan Anggrek',
            'blok_rumah' => $blokRumah,
            'rt' => $rt,
            'role_id' => Role::where('role_name', $roleName)->value('role_id'),
        ]);
    }
}
