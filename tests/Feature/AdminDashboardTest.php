<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'notifications',
            'pickup_status_histories',
            'withdrawals',
            'transactions',
            'pickups',
            'categories',
            'accounts',
            'users',
            'roles',
        ] as $table) {
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

        Schema::create('accounts', function (Blueprint $table): void {
            $table->increments('account_id');
            $table->unsignedInteger('user_id');
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->increments('category_id');
            $table->string('category_name', 100)->unique();
            $table->decimal('category_price', 10, 0);
            $table->string('category_image')->nullable();
            $table->timestamps();
        });

        Schema::create('pickups', function (Blueprint $table): void {
            $table->increments('pickup_id');
            $table->unsignedInteger('account');
            $table->dateTime('pickup_schedule')->nullable();
            $table->dateTime('pickup_finished')->nullable();
            $table->string('pickup_type');
            $table->string('pickup_status');
            $table->decimal('waste_weight', 10, 2)->nullable();
            $table->unsignedInteger('user_category')->nullable();
            $table->unsignedInteger('category')->nullable();
            $table->string('waste_condition')->nullable();
            $table->string('pickup_note')->nullable();
            $table->unsignedInteger('officer')->nullable();
            $table->string('waste_photo')->nullable();
            $table->string('pickup_address')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->increments('transaction_id');
            $table->unsignedInteger('pickup');
            $table->timestamp('transaction_date')->useCurrent();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('transaction_status');
            $table->string('transaction_information')->nullable();
            $table->unsignedInteger('admin')->nullable();
            $table->timestamps();
        });

        Schema::create('pickup_status_histories', function (Blueprint $table): void {
            $table->increments('history_id');
            $table->unsignedInteger('pickup_id');
            $table->string('status');
            $table->string('description')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('withdrawals', function (Blueprint $table): void {
            $table->increments('withdrawal_id');
            $table->unsignedInteger('account_id');
            $table->decimal('amount', 15, 2);
            $table->string('withdrawal_method', 50);
            $table->string('destination_name', 100)->nullable();
            $table->string('destination_number', 50)->nullable();
            $table->string('withdrawal_status');
            $table->unsignedInteger('processed_by')->nullable();
            $table->text('withdrawal_note')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->increments('notification_id');
            $table->unsignedInteger('user_id');
            $table->string('title', 100);
            $table->text('message');
            $table->string('notification_type', 50);
            $table->unsignedInteger('pickup_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('withdrawal_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Role::create(['role_name' => 'Admin']);
        Role::create(['role_name' => 'Warga']);
        Role::create(['role_name' => 'Petugas']);
    }

    public function test_sidebar_matches_reference_and_has_animated_data_menu_and_logout_icon(): void
    {
        $admin = $this->createUser('admin-sidebar', 'Admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('<div class="nav-group">', false)
            ->assertSee('aria-expanded="false" aria-controls="dashboard-data-subnav"', false)
            ->assertSee('aria-hidden="true"', false)
            ->assertSee('inert', false)
            ->assertSee('<a class="nav-link active" href="'.route('admin.dashboard').'" aria-current="page">', false)
            ->assertSee('left: -12px;', false)
            ->assertSee('width: 3px;', false)
            ->assertSee('background: #fff;', false)
            ->assertSee(route('admin.warga.index'))
            ->assertSee('Data Warga')
            ->assertSee('Data Petugas')
            ->assertSee('<a class="subnav-item" href="'.route('admin.petugas.index').'">Data Petugas</a>', false)
            ->assertSee('Transaksi Sampah')
            ->assertSee(asset('icons/Cart-icon.png'))
            ->assertSee('<a class="nav-link" href="'.route('admin.profile.show').'"', false)
            ->assertSee('Profile')
            ->assertDontSee('class="nav-link unavailable"', false)
            ->assertSee('.nav-group.is-open .nav-heading .nav-icon', false)
            ->assertSee('transition: transform 220ms ease;', false)
            ->assertSee('transform: scale(1.08);', false)
            ->assertDontSee('scale(1.08) rotate(4deg)')
            ->assertSee('padding-right: 31px;', false)
            ->assertSee('padding-left: 31px;', false)
            ->assertSee('text-align: center;', false)
            ->assertSee('transition: max-height 420ms ease-in-out', false)
            ->assertSee('max-height: 120px;', false)
            ->assertSee('transform: rotate(180deg);', false)
            ->assertSee('transform: rotate(270deg);', false)
            ->assertSee(asset('icons/arrow-icon.png'))
            ->assertSee(asset('icons/logout-icon.png'))
            ->assertSee('Log Out')
            ->assertSee('apakah anda yakin untuk log out?')
            ->assertSee('data-logout-confirmation-open', false)
            ->assertSee('data-logout-confirmation-cancel', false)
            ->assertSee('<button class="logout-confirmation-button logout-confirmation-submit" type="submit">Log out</button>', false)
            ->assertSee('event.target === dialog', false)
            ->assertSee('.logout-button {', false)
            ->assertSee('border: 0;', false);
    }

    public function test_dashboard_uses_live_database_metrics_transactions_and_period_filter(): void
    {
        $admin = $this->createUser('admin-dashboard', 'Admin');
        $citizen = $this->createUser('warga-dashboard', 'Warga', 'Nama Warga');
        $this->createUser('staff-dashboard', 'Petugas');
        $accountId = DB::table('accounts')->insertGetId([
            'user_id' => $citizen->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Plastik',
            'category_price' => 3000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $completedPickupId = $this->createPickup($accountId, $categoryId, 'Selesai', 2.5, '2026-03-10 12:00:00');
        $cancelledPickupId = $this->createPickup($accountId, $categoryId, 'Selesai', 8, '2026-03-11 12:00:00');
        $this->createPickup($accountId, $categoryId, 'Menunggu', 12, '2026-03-12 12:00:00');
        DB::table('categories')->insert([
            'category_name' => 'Kertas',
            'category_price' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createTransaction($completedPickupId, 'Selesai', 25000, '2026-03-10 12:00:00');
        $this->createTransaction($cancelledPickupId, 'Dibatalkan', 80000, '2026-03-11 12:00:00');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['month' => 3, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Hello, admin-dashboard!')
            ->assertDontSee('<p class="topbar-title">Dashboard</p>', false)
            ->assertDontSee('<div class="topbar-user">', false)
            ->assertSee('Total Warga')
            ->assertSee('>1</strong>', false)
            ->assertSee('>2</strong>', false)
            ->assertSee('2,50 kg')
            ->assertSee('Jenis Sampah')
            ->assertSee('Bulan Ini')
            ->assertSee('1 Mar 2026 - 31 Mar 2026')
            ->assertSee('month=2&amp;year=2026', false)
            ->assertSee('month=4&amp;year=2026', false)
            ->assertSee('Nama Warga')
            ->assertSee('RP. 25.000')
            ->assertSee('#1')
            ->assertSee('Plastik')
            ->assertDontSee('Kertas')
            ->assertSee('2,50 kg')
            ->assertSee(asset('icons/Warga-icon.png'))
            ->assertSee(asset('icons/Transaksi-icon.png'))
            ->assertSee(asset('icons/Beratsmph-icon.png'))
            ->assertSee(asset('icons/Ilusdashboard-admin.png'))
            ->assertSee(asset('icons/dashboard-icon.png'));
    }

    public function test_dashboard_shows_empty_states_for_periods_without_completed_pickups(): void
    {
        $admin = $this->createUser('admin-empty', 'Admin');
        DB::table('categories')->insert([
            'category_name' => 'Kertas',
            'category_price' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['month' => 1, 'year' => 2020]))
            ->assertOk()
            ->assertSee('Belum ada transaksi di database.')
            ->assertSee('Belum ada transaksi pada periode ini.')
            ->assertDontSee('Kertas')
            ->assertSee('Belum ada aktivitas atau notifikasi di database.')
            ->assertSee('Status operasional belum tersedia di database.');
    }

    public function test_transaction_pagination_shows_five_rows_per_page(): void
    {
        $admin = $this->createUser('admin-pages', 'Admin');
        $citizen = $this->createUser('warga-pages', 'Warga');
        $accountId = DB::table('accounts')->insertGetId([
            'user_id' => $citizen->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Kertas',
            'category_price' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($index = 1; $index <= 6; $index++) {
            $date = now()->subDays($index)->toDateTimeString();
            $pickupId = $this->createPickup($accountId, $categoryId, 'Selesai', 1, $date);
            $this->createTransaction($pickupId, 'Selesai', 1000, $date);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Showing 1 - 5 From 6')
            ->assertSee('page=2');

        $this->get(route('admin.dashboard', ['page' => 2]))
            ->assertOk()
            ->assertSee('Showing 6 - 6 From 6');
    }

    public function test_dashboard_shows_real_notification_and_pickup_history_activity(): void
    {
        $admin = $this->createUser('admin-activity', 'Admin');
        $citizen = $this->createUser('warga-activity', 'Warga');
        $accountId = DB::table('accounts')->insertGetId([
            'user_id' => $citizen->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Metal',
            'category_price' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $pickupId = $this->createPickup($accountId, $categoryId, 'Dikonfirmasi', 1, now()->toDateTimeString());

        DB::table('pickup_status_histories')->insert([
            'pickup_id' => $pickupId,
            'status' => 'Dikonfirmasi',
            'description' => 'Jadwal pickup dikonfirmasi',
            'updated_by' => $admin->user_id,
            'created_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $citizen->user_id,
            'title' => 'Pengajuan diperbarui',
            'message' => 'Pengajuan sampah menunggu penjemputan',
            'notification_type' => 'pickup',
            'pickup_id' => $pickupId,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pengajuan diperbarui')
            ->assertSee('Pengajuan sampah menunggu penjemputan')
            ->assertSee('Status pengajuan: Dikonfirmasi')
            ->assertSee('Jadwal pickup dikonfirmasi');
    }

    public function test_invalid_dashboard_period_is_rejected(): void
    {
        $this->actingAs($this->createUser('admin-filter', 'Admin'))
            ->get(route('admin.dashboard', ['month' => 13, 'year' => 2026]))
            ->assertRedirect()
            ->assertSessionHasErrors('month');
    }

    private function createUser(string $username, string $roleName, ?string $fullName = null): User
    {
        return User::create([
            'username' => $username,
            'full_name' => $fullName,
            'password_hash' => Hash::make('Password-12345'),
            'role_id' => Role::where('role_name', $roleName)->value('role_id'),
        ]);
    }

    private function createPickup(
        int $accountId,
        int $categoryId,
        string $status,
        float $weight,
        string $finishedAt
    ): int {
        return DB::table('pickups')->insertGetId([
            'account' => $accountId,
            'pickup_schedule' => $finishedAt,
            'pickup_finished' => $finishedAt,
            'pickup_type' => 'Langsung',
            'pickup_status' => $status,
            'waste_weight' => $weight,
            'category' => $categoryId,
            'created_at' => $finishedAt,
            'updated_at' => $finishedAt,
        ]);
    }

    private function createTransaction(int $pickupId, string $status, float $amount, string $date): void
    {
        DB::table('transactions')->insert([
            'pickup' => $pickupId,
            'transaction_date' => $date,
            'amount' => $amount,
            'transaction_status' => $status,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
