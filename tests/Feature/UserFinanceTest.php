<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserFinanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['withdrawals', 'transactions', 'pickups', 'categories', 'accounts', 'users', 'roles'] as $table) {
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

        foreach (['Admin', 'Warga'] as $roleName) {
            Role::create(['role_name' => $roleName]);
        }
    }

    protected function tearDown(): void
    {
        foreach (['withdrawals', 'transactions', 'pickups', 'categories', 'accounts', 'users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_finance_page_shows_only_current_wargas_completed_totals_and_monthly_activity(): void
    {
        $warga = $this->createUser('finance-warga');
        $otherWarga = $this->createUser('finance-other-warga');
        $accountId = $this->createAccount($warga, 50000);
        $otherAccountId = $this->createAccount($otherWarga, 90000);
        $categoryId = (int) DB::table('categories')->insertGetId([
            'category_name' => 'Kardus',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $pickupId = $this->createPickup($accountId, $categoryId);
        $otherPickupId = $this->createPickup($otherAccountId, $categoryId);

        $this->createTransaction($pickupId, 'Selesai', 18000, now()->subDay());
        $this->createTransaction($pickupId, 'Sedang Diproses', 99000, now()->subHours(2));
        $this->createTransaction($otherPickupId, 'Selesai', 70000, now()->subHours(1));
        $this->createTransaction($pickupId, 'Selesai', 3000, now()->subMonthNoOverflow());
        $this->createWithdrawal($accountId, 'Selesai', 4000, now()->subHours(3));
        $this->createWithdrawal($accountId, 'Menunggu Konfirmasi', 5000, now()->subHours(2));
        $this->createWithdrawal($otherAccountId, 'Selesai', 6000, now()->subHour());
        $this->createWithdrawal($accountId, 'Selesai', 1000, now()->subMonthNoOverflow());

        $this->actingAs($warga)
            ->get(route('user.keuangan', ['month' => now()->format('Y-m')]))
            ->assertOk()
            ->assertSee('Saldo Anda')
            ->assertSee('Rp 50.000')
            ->assertSee('Total pemasukan')
            ->assertSee('Rp 21.000')
            ->assertSee('Total pencairan')
            ->assertSee('Rp 5.000')
            ->assertSee('Penjualan Kardus')
            ->assertSee('+ Rp 18.000')
            ->assertSee('- Rp 4.000')
            ->assertSee('Menunggu Konfirmasi')
            ->assertDontSee('+ Rp 3.000')
            ->assertDontSee('- Rp 1.000')
            ->assertDontSee('Rp 70.000')
            ->assertDontSee('- Rp 6.000')
            ->assertSee('type="month"', false)
            ->assertSee('value="'.now()->format('Y-m').'"', false)
            ->assertSee('href="'.route('user.keuangan.withdrawals.create').'"', false);
    }

    public function test_withdrawal_form_shows_available_balance_and_all_methods(): void
    {
        $warga = $this->createUser('finance-form-warga');
        $accountId = $this->createAccount($warga, 50000);
        $this->createWithdrawal($accountId, 'Menunggu Konfirmasi', 10000, now()->subHour());

        $this->actingAs($warga)
            ->get(route('user.keuangan.withdrawals.create'))
            ->assertOk()
            ->assertSee('Pencairan Uang')
            ->assertSee('Saldo tersisa: Rp 40.000')
            ->assertSee('Datang langsung')
            ->assertSee('ShopeePay')
            ->assertSee('DANA')
            ->assertSee('BCA Mobile')
            ->assertSee('type="radio"', false)
            ->assertSee('Menunggu konfirmasi')
            ->assertSee('name="withdrawal_method"', false)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('id="withdrawal-amount-value"', false);
    }

    public function test_withdrawal_form_saves_each_supported_method_without_debiting_balance(): void
    {
        $warga = $this->createUser('finance-method-warga');
        $accountId = $this->createAccount($warga, 50000);

        foreach (['Datang langsung', 'ShopeePay', 'DANA', 'BCA Mobile'] as $method) {
            $this->actingAs($warga)
                ->post(route('user.keuangan.withdrawals.store'), [
                    'amount' => 1000,
                    'withdrawal_method' => $method,
                ])
                ->assertRedirect(route('user.keuangan'));
        }

        $this->assertSame(
            ['BCA Mobile', 'DANA', 'Datang langsung', 'ShopeePay'],
            DB::table('withdrawals')
                ->where('account_id', $accountId)
                ->orderBy('withdrawal_method')
                ->pluck('withdrawal_method')
                ->all()
        );
        $this->assertEquals(50000, (float) DB::table('accounts')->where('account_id', $accountId)->value('balance'));

        $this->actingAs($warga)
            ->from(route('user.keuangan.withdrawals.create'))
            ->post(route('user.keuangan.withdrawals.store'), [
                'amount' => 1000,
                'withdrawal_method' => 'Metode palsu',
            ])
            ->assertRedirect(route('user.keuangan.withdrawals.create'))
            ->assertSessionHasErrors('withdrawal_method');

        $this->assertSame(4, DB::table('withdrawals')->where('account_id', $accountId)->count());
    }

    public function test_withdrawal_request_is_pending_does_not_debit_balance_and_respects_reserved_requests(): void
    {
        $warga = $this->createUser('finance-request-warga');
        $accountId = $this->createAccount($warga, 50000);
        $this->createWithdrawal($accountId, 'Menunggu Konfirmasi', 20000, now()->subHour());

        $this->actingAs($warga)
            ->from(route('user.keuangan'))
            ->post(route('user.keuangan.withdrawals.store'), [
                'amount' => 30000.01,
                'withdrawal_method' => 'Datang langsung',
            ])
            ->assertRedirect(route('user.keuangan'))
            ->assertSessionHasErrors('amount');

        $this->assertSame(1, DB::table('withdrawals')->where('account_id', $accountId)->count());

        $this->actingAs($warga)
            ->post(route('user.keuangan.withdrawals.store'), [
                'amount' => 25000,
                'withdrawal_method' => 'Datang langsung',
            ])
            ->assertRedirect(route('user.keuangan'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('withdrawals', [
            'account_id' => $accountId,
            'amount' => 25000,
            'withdrawal_method' => 'Datang langsung',
            'withdrawal_status' => 'Menunggu Konfirmasi',
        ]);
        $this->assertEquals(50000, (float) DB::table('accounts')->where('account_id', $accountId)->value('balance'));

        $this->actingAs($warga)
            ->get(route('user.keuangan.withdrawals.create'))
            ->assertOk()
            ->assertSee('Saldo tersisa: Rp 5.000');
    }

    public function test_finance_page_has_an_empty_state_for_a_warga_without_an_account(): void
    {
        $warga = $this->createUser('finance-empty-warga');

        $this->actingAs($warga)
            ->get(route('user.keuangan'))
            ->assertOk()
            ->assertSee('Belum ada transaksi keuangan pada bulan ini.')
            ->assertSee('Rp 0');
    }

    public function test_guest_is_redirected_and_non_warga_cannot_access_finance(): void
    {
        $this->get(route('user.keuangan'))
            ->assertRedirect(route('login'));

        $admin = User::create([
            'username' => 'finance-admin',
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Admin')->value('role_id'),
        ]);

        $this->actingAs($admin)
            ->get(route('user.keuangan'))
            ->assertForbidden();
    }

    private function createUser(string $username): User
    {
        return User::create([
            'username' => $username,
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
        ]);
    }

    private function createAccount(User $user, float $balance): int
    {
        return (int) DB::table('accounts')->insertGetId([
            'user_id' => $user->user_id,
            'balance' => $balance,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPickup(int $accountId, int $categoryId): int
    {
        return (int) DB::table('pickups')->insertGetId([
            'account' => $accountId,
            'pickup_schedule' => now(),
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Selesai',
            'waste_weight' => 2.5,
            'category' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTransaction(int $pickupId, string $status, float $amount, CarbonInterface $date): void
    {
        DB::table('transactions')->insert([
            'pickup' => $pickupId,
            'transaction_date' => $date,
            'amount' => $amount,
            'transaction_status' => $status,
            'transaction_information' => null,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    private function createWithdrawal(int $accountId, string $status, float $amount, CarbonInterface $date): void
    {
        DB::table('withdrawals')->insert([
            'account_id' => $accountId,
            'amount' => $amount,
            'withdrawal_method' => 'Datang langsung',
            'withdrawal_status' => $status,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
