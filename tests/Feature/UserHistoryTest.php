<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserHistoryTest extends TestCase
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
            $table->unsignedInteger('role_id');
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

        foreach (['Admin', 'Warga'] as $roleName) {
            Role::create(['role_name' => $roleName]);
        }
    }

    protected function tearDown(): void
    {
        foreach (['transactions', 'pickups', 'categories', 'accounts', 'users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_history_shows_only_the_current_wargas_pickups_and_transactions_in_date_order(): void
    {
        $warga = $this->createUser('warga-history');
        $otherWarga = $this->createUser('other-warga-history');
        $accountId = $this->createAccount($warga);
        $otherAccountId = $this->createAccount($otherWarga);
        $plasticCategory = $this->createCategory('Plastik Botol');
        $cardboardCategory = $this->createCategory('Kardus');
        $paperCategory = $this->createCategory('Kertas');

        $this->createPickup(
            $accountId,
            $plasticCategory,
            'Menunggu',
            2.5,
            now()->subHour()
        );
        $this->createPickup(
            $accountId,
            $cardboardCategory,
            'Ditolak',
            1.25,
            now()->subDays(2)
        );
        $finishedPickup = $this->createPickup(
            $accountId,
            $paperCategory,
            'Selesai',
            3,
            now()->subDays(3)
        );
        DB::table('transactions')->insert([
            'pickup' => $finishedPickup,
            'transaction_date' => now()->subMinutes(30),
            'amount' => 18000,
            'transaction_status' => 'Selesai',
            'transaction_information' => null,
            'created_at' => now()->subMinutes(30),
            'updated_at' => now()->subMinutes(30),
        ]);

        $otherCategory = $this->createCategory('Milik Warga Lain');
        $this->createPickup($otherAccountId, $otherCategory, 'Menunggu', 20, now());

        $this->actingAs($warga)
            ->get(route('user.riwayat'))
            ->assertOk()
            ->assertSee('href="'.route('user.dashboard').'"', false)
            ->assertSee('Penjualan Plastik Botol')
            ->assertSee('2,5 kg · Pengajuan Sampah')
            ->assertSee('Menunggu')
            ->assertSee('Ditolak')
            ->assertSee('Dana masuk')
            ->assertSee('Hasil Penjualan Kertas')
            ->assertSee('+ Rp 18.000')
            ->assertSee('data-history-kind="pickup"', false)
            ->assertSee('data-history-kind="transaction"', false)
            ->assertSee('data-history-status="menunggu"', false)
            ->assertDontSee('Penjualan Milik Warga Lain');
    }

    public function test_history_page_has_empty_state_for_a_warga_without_an_account(): void
    {
        $warga = $this->createUser('warga-empty-history');

        $this->actingAs($warga)
            ->get(route('user.riwayat'))
            ->assertOk()
            ->assertSee('Belum ada riwayat pengajuan atau transaksi.');
    }

    public function test_guest_is_redirected_to_login_from_history(): void
    {
        $this->get(route('user.riwayat'))
            ->assertRedirect(route('login'));
    }

    private function createUser(string $username): User
    {
        return User::create([
            'username' => $username,
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
        ]);
    }

    private function createAccount(User $user): int
    {
        return (int) DB::table('accounts')->insertGetId([
            'user_id' => $user->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createCategory(string $name): int
    {
        return (int) DB::table('categories')->insertGetId([
            'category_name' => $name,
            'category_price' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPickup(
        int $accountId,
        int $categoryId,
        string $status,
        float $weight,
        Carbon $createdAt
    ): int {
        return (int) DB::table('pickups')->insertGetId([
            'account' => $accountId,
            'pickup_schedule' => $createdAt,
            'pickup_finished' => $status === 'Selesai' ? $createdAt : null,
            'pickup_type' => 'Langsung',
            'pickup_status' => $status,
            'waste_weight' => $weight,
            'category' => $categoryId,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
