<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransaksiSampahTest extends TestCase
{
    protected User $admin;

    protected User $citizen;

    protected int $accountId;

    protected int $categoryId;

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

        Role::create(['role_name' => 'Admin']);
        Role::create(['role_name' => 'Warga']);
        Role::create(['role_name' => 'Petugas']);
        $this->admin = $this->createUser('admin-transaksi', 'Admin');
        $this->citizen = $this->createUser('warga-transaksi', 'Warga', 'Warga Contoh');
        $this->accountId = DB::table('accounts')->insertGetId([
            'user_id' => $this->citizen->user_id,
            'balance' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->categoryId = DB::table('categories')->insertGetId([
            'category_name' => 'Plastik',
            'category_price' => 3000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_index_search_filter_and_pagination_use_database_records(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $this->createTransaction('Menunggu', 0, '2026-03-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT).' 10:15:00');
        }
        $paidId = $this->createTransaction('Selesai', 4321, '2026-03-20 11:25:00');

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.index'))
            ->assertOk()
            ->assertSee('warga-transaksi')
            ->assertSee('<a class="admin-nav-link active" href="'.route('admin.transaksi.index').'"', false)
            ->assertDontSee('<a class="admin-subnav-item active"', false)
            ->assertViewHas('transactions', function ($transactions): bool {
                return $transactions->perPage() === 10
                    && $transactions->total() === 12
                    && $transactions->nextPageUrl() !== null;
            });

        foreach ([(string) $paidId, '4321'] as $term) {
            $this->actingAs($this->admin)
                ->get(route('admin.transaksi.index', ['search' => $term]))
                ->assertOk()
                ->assertViewHas('transactions', fn ($transactions): bool => $transactions->total() === 1
                    && $transactions->first()->transaction_id === $paidId);
        }

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.index', ['search' => 'warga-transaksi', 'status' => 'Menunggu']))
            ->assertOk()
            ->assertSee('Data Transaksi')
            ->assertSee('warga-transaksi');

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.index', ['date_from' => '2026-03-10', 'date_to' => '2026-03-11']))
            ->assertOk()
            ->assertViewHas('transactions', fn ($transactions): bool => $transactions->total() === 2);
    }

    public function test_add_edit_delete_and_payment_preserve_transaction_invariants(): void
    {
        $pendingPickup = $this->createPickup();
        $pickupDate = DB::table('pickups')->where('pickup_id', $pendingPickup)->value('pickup_finished');
        $balanceBefore = DB::table('accounts')->where('account_id', $this->accountId)->value('balance');

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.create'))
            ->assertOk()
            ->assertSee('Tambah Transaksi Sampah')
            ->assertSee('warga-transaksi')
            ->assertSee('#'.$pendingPickup)
            ->assertSee('data-preview="date"', false)
            ->assertSee('id="transaction-date" type="date"', false)
            ->assertSee('placeholder="Pilih Waktu"', false)
            ->assertSee('placeholder="Masukan Nominal"', false)
            ->assertSee(asset('icons/arrow-icon.png'))
            ->assertSee('<a class="admin-nav-link active" href="'.route('admin.transaksi.index').'"', false)
            ->assertDontSee('<a class="admin-subnav-item active"', false);

        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.store'), ['pickup' => $pendingPickup])
            ->assertRedirect(route('admin.transaksi.index'))
            ->assertSessionHas('status');

        $transaction = DB::table('transactions')->where('pickup', $pendingPickup)->first();
        $transactionId = (int) $transaction->transaction_id;
        $this->assertSame((int) $pendingPickup, (int) $transaction->pickup);
        $this->assertEquals(7500, (float) $transaction->amount);
        $this->assertSame($pickupDate, $transaction->transaction_date);
        $this->assertSame($balanceBefore, DB::table('accounts')->where('account_id', $this->accountId)->value('balance'));

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.index', ['search' => 'warga-transaksi']))
            ->assertOk()
            ->assertSee('#'.$transactionId);

        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.store'), ['pickup' => $pendingPickup])
            ->assertSessionHasErrors('pickup');
        $this->assertSame(1, DB::table('transactions')->where('pickup', $pendingPickup)->count());
        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.create'))
            ->assertOk()
            ->assertDontSee('value="'.$pendingPickup.'"', false);

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.edit', $transactionId))
            ->assertOk()
            ->assertSee('Edit Transaksi');

        $this->actingAs($this->admin)
            ->put(route('admin.transaksi.update', $transactionId), [
                'pickup' => $pendingPickup,
                'transaction_date' => '2026-03-12T10:30',
                'transaction_information' => 'Hasil penimbangan',
            ])
            ->assertRedirect(route('admin.transaksi.index'));

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.payment', $transactionId))
            ->assertOk()
            ->assertSee('Konfirmasi Pembayaran')
            ->assertSee('value="7500"', false);

        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.pay', $transactionId), ['amount' => 25000])
            ->assertRedirect(route('admin.transaksi.index'))
            ->assertSessionHas('status');

        $this->assertEquals(25000, (float) DB::table('transactions')->where('transaction_id', $transactionId)->value('amount'));
        $this->assertSame('Selesai', DB::table('transactions')->where('transaction_id', $transactionId)->value('transaction_status'));
        $this->assertEquals(25100, (float) DB::table('accounts')->where('account_id', $this->accountId)->value('balance'));

        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.pay', $transactionId), ['amount' => 25000])
            ->assertSessionHasErrors('amount');
        $this->assertEquals(25100, (float) DB::table('accounts')->where('account_id', $this->accountId)->value('balance'));

        $this->actingAs($this->admin)
            ->delete(route('admin.transaksi.destroy', $transactionId))
            ->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('transactions', ['transaction_id' => $transactionId, 'transaction_status' => 'Selesai']);

        $deletePickup = $this->createPickup();
        $deleteId = $this->createTransaction('Menunggu', 0, now()->toDateTimeString(), $deletePickup);
        $this->actingAs($this->admin)
            ->delete(route('admin.transaksi.destroy', $deleteId))
            ->assertRedirect(route('admin.transaksi.index'));
        $this->assertDatabaseMissing('transactions', ['transaction_id' => $deleteId]);
    }

    public function test_pdf_export_downloads_real_transaction_report(): void
    {
        $this->createTransaction('Selesai', 15000, '2026-03-10 10:15:00');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.transaksi.pdf'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_manual_entry_rejects_nonexistent_unfinished_and_non_citizen_pickups(): void
    {
        $unfinishedPickup = $this->createPickup('Menunggu');
        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.store'), ['pickup' => $unfinishedPickup])
            ->assertSessionHasErrors('pickup');

        $staff = $this->createUser('petugas-transaksi', 'Petugas');
        $staffAccountId = DB::table('accounts')->insertGetId([
            'user_id' => $staff->user_id,
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $staffPickup = DB::table('pickups')->insertGetId([
            'account' => $staffAccountId,
            'pickup_schedule' => now(),
            'pickup_finished' => now(),
            'pickup_type' => 'Langsung',
            'pickup_status' => 'Selesai',
            'waste_weight' => 3,
            'category' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.store'), ['pickup' => $staffPickup])
            ->assertSessionHasErrors('pickup');

        $this->actingAs($this->admin)
            ->post(route('admin.transaksi.store'), ['pickup' => 99999])
            ->assertSessionHasErrors('pickup');

        $this->assertDatabaseMissing('transactions', ['pickup' => $unfinishedPickup]);
        $this->assertDatabaseMissing('transactions', ['pickup' => $staffPickup]);
    }

    public function test_create_form_keeps_reference_layout_visible_when_no_pickups_are_available(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.create'))
            ->assertOk()
            ->assertSee('Tambah Transaksi Sampah')
            ->assertSee('Username')
            ->assertSee('Belum ada pickup selesai')
            ->assertSee('id="transaction-date"', false)
            ->assertSee('id="transaction-time"', false)
            ->assertSee('id="transaction-amount"', false)
            ->assertSee('Submit')
            ->assertSee('id="pickup" name="pickup" required disabled', false)
            ->assertSee('class="submit-button" type="submit" disabled', false);
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

    private function createPickup(string $status = 'Selesai'): int
    {
        return DB::table('pickups')->insertGetId([
            'account' => $this->accountId,
            'pickup_schedule' => now(),
            'pickup_finished' => now(),
            'pickup_type' => 'Langsung',
            'pickup_status' => $status,
            'waste_weight' => 2.5,
            'category' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTransaction(string $status, float $amount, string $date, ?int $pickupId = null): int
    {
        return DB::table('transactions')->insertGetId([
            'pickup' => $pickupId ?? $this->createPickup(),
            'transaction_date' => $date,
            'amount' => $amount,
            'transaction_status' => $status,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
