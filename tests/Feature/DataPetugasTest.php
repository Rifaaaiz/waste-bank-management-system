<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DataPetugasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('pickups');
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

        Schema::create('pickups', function (Blueprint $table): void {
            $table->increments('pickup_id');
            $table->unsignedInteger('officer')->nullable();
        });

        foreach (['Admin', 'Petugas', 'Warga'] as $roleName) {
            Role::create(['role_name' => $roleName]);
        }
    }

    public function test_admin_can_search_filter_and_paginate_only_petugas_by_ten(): void
    {
        $admin = $this->createUser('admin-list', 'Admin');

        for ($number = 1; $number <= 13; $number++) {
            $this->createUser(
                sprintf('petugas-%02d', $number),
                'Petugas',
                sprintf('Nama Petugas %02d', $number),
                $number <= 11 ? 'Pengangkut' : 'Koordinator'
            );
        }
        $this->createUser('warga-list', 'Warga', 'Warga Bukan Petugas');

        $firstPage = $this->actingAs($admin)
            ->get(route('admin.petugas.index'))
            ->assertOk()
            ->assertSee('Data Petugas')
            ->assertSee('Search by invoice number, name, amount...')
            ->assertSee(route('admin.petugas.create'))
            ->assertSee(route('admin.petugas.pdf'))
            ->assertSee('Nama Petugas 01')
            ->assertDontSee('Nama Petugas 11')
            ->assertDontSee('Warga Bukan Petugas');

        $this->assertCount(10, $firstPage->viewData('petugas'));

        $this->get(route('admin.petugas.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Nama Petugas 11')
            ->assertDontSee('Nama Petugas 01');

        $filtered = $this->get(route('admin.petugas.index', [
            'search' => 'petugas-',
            'position' => 'Pengangkut',
        ]))
            ->assertOk()
            ->assertSee('Nama Petugas 01')
            ->assertDontSee('Nama Petugas 12')
            ->assertSee(route('admin.petugas.index', [
                'search' => 'petugas-',
                'position' => 'Pengangkut',
                'page' => 2,
            ]));

        $this->assertCount(10, $filtered->viewData('petugas'));
    }

    public function test_admin_can_create_update_and_delete_petugas_without_changing_role(): void
    {
        $admin = $this->createUser('admin-crud', 'Admin');

        $this->actingAs($admin)
            ->get(route('admin.petugas.create'))
            ->assertOk()
            ->assertSee('Tambah Data Petugas')
            ->assertSee('admin-subnav-item active', false)
            ->assertSee('Data Warga</a>', false)
            ->assertDontSee('class="admin-subnav-item active" href="'.route('admin.warga.index').'"', false);

        $this->post(route('admin.petugas.store'), [
            'full_name' => 'Petugas Baru',
            'username' => 'petugas-baru',
            'phone_number' => '08123456789',
            'position' => 'Pengangkut',
            'password' => 'Password-aman-123',
            'password_confirmation' => 'Password-aman-123',
            'role_id' => Role::where('role_name', 'Admin')->value('role_id'),
        ])->assertRedirect(route('admin.petugas.index'));

        $petugas = User::where('username', 'petugas-baru')->firstOrFail();
        $this->assertSame('Petugas', $petugas->role->role_name);
        $this->assertTrue(Hash::check('Password-aman-123', $petugas->password_hash));

        $this->get(route('admin.petugas.edit', $petugas->user_id))
            ->assertOk()
            ->assertSee('Edit Data Petugas')
            ->assertSee('Petugas Baru');

        $this->put(route('admin.petugas.update', $petugas->user_id), [
            'full_name' => 'Petugas Diperbarui',
            'username' => 'petugas-baru',
            'phone_number' => '08123456789',
            'position' => 'Koordinator',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.petugas.index'));

        $petugas->refresh();
        $this->assertSame('Petugas Diperbarui', $petugas->full_name);
        $this->assertSame('Koordinator', $petugas->position);
        $this->assertTrue(Hash::check('Password-aman-123', $petugas->password_hash));

        $this->delete(route('admin.petugas.destroy', $petugas->user_id))
            ->assertRedirect(route('admin.petugas.index'));
        $this->assertDatabaseMissing('users', ['user_id' => $petugas->user_id]);
    }

    public function test_non_petugas_accounts_cannot_be_edited_or_deleted_through_petugas_routes(): void
    {
        $admin = $this->createUser('admin-guard', 'Admin');
        $warga = $this->createUser('warga-guard', 'Warga');

        $this->actingAs($admin)
            ->get(route('admin.petugas.edit', $warga->user_id))
            ->assertNotFound();

        $this->delete(route('admin.petugas.destroy', $warga->user_id))
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['user_id' => $warga->user_id]);
    }

    public function test_assigned_petugas_cannot_be_deleted(): void
    {
        $admin = $this->createUser('admin-assigned', 'Admin');
        $petugas = $this->createUser('petugas-assigned', 'Petugas');
        DB::table('pickups')->insert(['officer' => $petugas->user_id]);

        $this->actingAs($admin)
            ->from(route('admin.petugas.index'))
            ->delete(route('admin.petugas.destroy', $petugas->user_id))
            ->assertRedirect(route('admin.petugas.index'))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('users', ['user_id' => $petugas->user_id]);
    }

    public function test_pdf_download_contains_petugas_report_from_database(): void
    {
        $admin = $this->createUser('admin-pdf-staff', 'Admin');
        $this->createUser('petugas-pdf', 'Petugas', 'Petugas PDF', 'Koordinator');

        $response = $this->actingAs($admin)
            ->get(route('admin.petugas.pdf', ['position' => 'Koordinator']));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-data-petugas.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    private function createUser(
        string $username,
        string $roleName,
        ?string $fullName = null,
        string $position = 'Pengangkut'
    ): User {
        return User::create([
            'username' => $username,
            'password_hash' => Hash::make('Password-aman-123'),
            'full_name' => $fullName ?? $username,
            'phone_number' => '08123456789',
            'position' => $position,
            'role_id' => Role::where('role_name', $roleName)->value('role_id'),
        ]);
    }
}
