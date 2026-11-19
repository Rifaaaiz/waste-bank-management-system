<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['transactions', 'pickups', 'categories', 'accounts', 'users', 'roles'] as $table) {
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
            $table->string('category_name', 100);
            $table->decimal('category_price', 10, 0)->default(0);
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
            $table->unsignedInteger('category')->nullable();
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

        foreach (['Admin', 'Petugas', 'Warga'] as $roleName) {
            Role::create(['role_name' => $roleName]);
        }
    }

    public function test_warga_login_redirects_to_dashboard_and_shows_balance(): void
    {
        $user = User::create([
            'username' => 'warga-dashboard',
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
            'full_name' => 'Warga Test',
        ]);

        DB::table('accounts')->insert([
            'user_id' => $user->user_id,
            'balance' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $accountId = (int) DB::table('accounts')->where('user_id', $user->user_id)->value('account_id');
        $categoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Plastik',
            'category_price' => 3000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $pickupId = DB::table('pickups')->insertGetId([
            'account' => $accountId,
            'pickup_schedule' => now(),
            'pickup_finished' => now(),
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Selesai',
            'waste_weight' => 2.5,
            'category' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('transactions')->insert([
            'pickup' => $pickupId,
            'transaction_date' => now(),
            'amount' => 7500,
            'transaction_status' => 'Selesai',
            'transaction_information' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pickups')->insert([
            'account' => $accountId,
            'pickup_schedule' => now(),
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Menunggu',
            'waste_weight' => 2.5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $previousCategoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Kertas',
            'category_price' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $previousMonth = now()->subMonthNoOverflow();
        DB::table('pickups')->insert([
            'account' => $accountId,
            'pickup_schedule' => $previousMonth,
            'pickup_finished' => $previousMonth,
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Selesai',
            'waste_weight' => 4,
            'category' => $previousCategoryId,
            'created_at' => $previousMonth,
            'updated_at' => $previousMonth,
        ]);

        $otherUser = User::create([
            'username' => 'another-warga',
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
            'full_name' => 'Warga Lain',
        ]);
        $otherAccountId = DB::table('accounts')->insertGetId([
            'user_id' => $otherUser->user_id,
            'balance' => 90000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherCategoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Aluminium',
            'category_price' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pickups')->insert([
            'account' => $otherAccountId,
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Menunggu',
            'waste_weight' => 20,
            'category' => $otherCategoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('login.store'), [
            'username' => 'warga-dashboard',
            'password' => 'Password-123',
        ])->assertRedirect(route('user.dashboard'))
            ->assertSessionHas('status');

        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->get(route('user.dashboard'));
        $dashboardResponse
            ->assertOk()
            ->assertSee('Hai, Warga Test')
            ->assertSee('RP.')
            ->assertSee('50.000')
            ->assertSee('Hasil penjualan Plastik')
            ->assertSee(route('user.riwayat'))
            ->assertSee(route('user.notifications.index'))
            ->assertSee(route('user.keuangan.withdrawals.create'))
            ->assertSee(route('user.profile.show'))
            ->assertSee('Dana masuk')
            ->assertSee('Menunggu')
            ->assertSee('2,5 kg')
            ->assertSee('Ringkasan Sampah')
            ->assertSee('Sampah Paling Sering Disetorkan')
            ->assertSee('Total')
            ->assertSee('2,5')
            ->assertSee(asset('icons/Eye-icon.png'))
            ->assertSee(asset('icons/Botolhijau-icon.png'))
            ->assertSee('segmented-nav.statistics-active::before')
            ->assertSee('panel-switch-forward')
            ->assertDontSee('@view-transition')
            ->assertDontSee('Warga Lain')
            ->assertDontSee('Penjualan Aluminium')
            ->assertDontSee('20,00 kg')
            ->assertDontSee('<span>Kertas</span>', false);

        self::assertSame(1, substr_count($dashboardResponse->getContent(), 'Dana masuk'));
        self::assertStringNotContainsString('Penjualan Plastik', $dashboardResponse->getContent());

        $this->assertStringContainsString(
            '<a class="view-all-link" href="'.route('user.riwayat').'">',
            $dashboardResponse->getContent()
        );
        $this->assertStringContainsString(
            '<a class="withdraw-button" href="'.route('user.keuangan.withdrawals.create').'">Tarik Saldo</a>',
            $dashboardResponse->getContent()
        );

        $periodStart = $previousMonth->copy()->locale('id')->startOfMonth()->translatedFormat('j M Y');
        $periodEnd = $previousMonth->copy()->locale('id')->endOfMonth()->translatedFormat('j M Y');
        $oldActivityDate = $previousMonth->copy()->locale('id')->translatedFormat('d M Y, H.i');

        $this->get(route('user.dashboard', [
            'month' => $previousMonth->format('Y-m'),
            'tab' => 'statistics',
        ]))
            ->assertOk()
            ->assertSee('Kertas')
            ->assertSee('4')
            ->assertSee($periodStart.' - '.$periodEnd)
            ->assertSee($oldActivityDate)
            ->assertSee('statistics');
    }

    public function test_dashboard_handles_missing_optional_source_tables(): void
    {
        $user = User::create([
            'username' => 'warga-no-optional-tables',
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
            'full_name' => 'Warga Tanpa Tabel Opsional',
        ]);
        $accountId = DB::table('accounts')->insertGetId([
            'user_id' => $user->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pickups')->insert([
            'account' => $accountId,
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Menunggu',
            'waste_weight' => 0.75,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Schema::dropIfExists('categories');
        Schema::dropIfExists('transactions');

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Pengajuan sampah')
            ->assertSee('0,75 kg')
            ->assertSee('Belum ada data setoran pada periode ini.');

        Schema::dropIfExists('pickups');
        Schema::dropIfExists('accounts');

        $this->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada aktivitas terbaru untuk akun ini.')
            ->assertSee('Belum ada data setoran pada periode ini.');
    }

    public function test_guest_cannot_access_dashboard_directly(): void
    {
        $this->get(route('user.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_login_keeps_its_admin_dashboard_redirect(): void
    {
        $admin = User::create([
            'username' => 'admin-user-login',
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Admin')->value('role_id'),
        ]);

        $this->post(route('login.store'), [
            'username' => $admin->username,
            'password' => 'Password-123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('login'))->assertRedirect(route('admin.dashboard'));
    }
}
