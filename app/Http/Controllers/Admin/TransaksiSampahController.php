<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class TransaksiSampahController extends Controller
{
    private const PAGE_SIZE = 10;

    private const PAYABLE_STATUS = 'Menunggu';

    private const PAID_STATUS = 'Selesai';

    private const CANCELLED_STATUS = 'Dibatalkan';

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', Rule::in([self::PAYABLE_STATUS, self::PAID_STATUS, self::CANCELLED_STATUS])],
            'date_from' => ['sometimes', 'nullable', 'date'],
            'date_to' => ['sometimes', 'nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $transactions = $this->filteredTransactionQuery($filters)
            ->orderByDesc('transactions.transaction_date')
            ->orderByDesc('transactions.transaction_id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('admin.transaksisampah', compact('transactions', 'filters'));
    }

    public function create(): View
    {
        return view('admin.tambahtransaksisampah', [
            'pickups' => $this->eligiblePickupQuery()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pickup' => ['required', 'integer'],
        ]);

        DB::transaction(function () use ($validated): void {
            $pickupId = (int) $validated['pickup'];

            DB::table('pickups')
                ->where('pickup_id', $pickupId)
                ->lockForUpdate()
                ->first();

            $pickup = $this->eligiblePickupQuery()
                ->where('pickups.pickup_id', $pickupId)
                ->first();

            if ($pickup === null) {
                throw ValidationException::withMessages([
                    'pickup' => 'Pickup tidak tersedia atau sudah memiliki transaksi.',
                ]);
            }

            $transactionDate = $pickup->pickup_finished
                ?? $pickup->updated_at
                ?? $pickup->created_at
                ?? now();

            DB::table('transactions')->insert([
                'pickup' => $pickup->pickup_id,
                'transaction_date' => $transactionDate,
                'amount' => $pickup->calculated_amount,
                'transaction_status' => self::PAYABLE_STATUS,
                'transaction_information' => null,
                'admin' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.transaksi.index')
            ->with('status', 'Catatan transaksi berhasil ditambahkan. Saldo warga belum berubah; selesaikan pembayaran melalui tombol Bayar.');
    }

    public function edit(int $transaction): View
    {
        $item = $this->transactionQuery()
            ->where('transactions.transaction_id', $transaction)
            ->firstOrFail();
        $this->ensurePayable($item);

        return view('admin.edittransaksisampah', [
            'transaction' => $item,
            'pickups' => $this->eligiblePickupQuery($item->pickup)->get(),
        ]);
    }

    public function update(Request $request, int $transaction): RedirectResponse
    {
        $item = $this->transactionQuery()
            ->where('transactions.transaction_id', $transaction)
            ->firstOrFail();
        $this->ensurePayable($item);

        $validated = $request->validate([
            'pickup' => ['required', 'integer'],
            'transaction_date' => ['required', 'date'],
            'transaction_information' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $transaction): void {
            $current = DB::table('transactions')
                ->where('transaction_id', $transaction)
                ->lockForUpdate()
                ->first();

            if ($current === null || $current->transaction_status !== self::PAYABLE_STATUS) {
                throw ValidationException::withMessages([
                    'transaction' => 'Transaksi sudah diproses dan tidak dapat diedit.',
                ]);
            }

            $pickup = DB::table('pickups')
                ->where('pickup_id', $validated['pickup'])
                ->where('pickup_status', 'Selesai')
                ->whereNotNull('waste_weight')
                ->lockForUpdate()
                ->first();

            if ($pickup === null || DB::table('transactions')
                ->where('pickup', $validated['pickup'])
                ->where('transaction_id', '!=', $transaction)
                ->exists()) {
                throw ValidationException::withMessages([
                    'pickup' => 'Pickup tidak tersedia atau sudah memiliki transaksi lain.',
                ]);
            }

            DB::table('transactions')
                ->where('transaction_id', $transaction)
                ->update([
                    'pickup' => $pickup->pickup_id,
                    'transaction_date' => $validated['transaction_date'],
                    'transaction_information' => $validated['transaction_information'] ?? null,
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('admin.transaksi.index')
            ->with('status', 'Data transaksi berhasil diperbarui.');
    }

    public function destroy(int $transaction): RedirectResponse
    {
        $deleted = DB::transaction(function () use ($transaction): bool {
            $item = DB::table('transactions')
                ->where('transaction_id', $transaction)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->transaction_status !== self::PAYABLE_STATUS) {
                return false;
            }

            return DB::table('transactions')
                ->where('transaction_id', $transaction)
                ->where('transaction_status', self::PAYABLE_STATUS)
                ->delete() === 1;
        });

        if (! $deleted) {
            return back()->withErrors([
                'delete' => 'Transaksi yang sudah diproses tidak dapat dihapus.',
            ]);
        }

        return redirect()
            ->route('admin.transaksi.index')
            ->with('status', 'Transaksi menunggu berhasil dihapus.');
    }

    public function showPayment(int $transaction): View
    {
        $item = $this->transactionQuery()
            ->where('transactions.transaction_id', $transaction)
            ->firstOrFail();
        $this->ensurePayable($item);

        return view('admin.bayartransaksisampah', [
            'transaction' => $item,
        ]);
    }

    public function pay(Request $request, int $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
        ]);

        DB::transaction(function () use ($request, $transaction, $validated): void {
            $item = DB::table('transactions')
                ->where('transaction_id', $transaction)
                ->lockForUpdate()
                ->first();

            if ($item === null || $item->transaction_status !== self::PAYABLE_STATUS) {
                throw ValidationException::withMessages([
                    'amount' => 'Transaksi ini sudah dibayar atau tidak dapat diproses.',
                ]);
            }

            $pickup = DB::table('pickups')
                ->where('pickup_id', $item->pickup)
                ->lockForUpdate()
                ->first();

            $account = $pickup
                ? DB::table('accounts')->where('account_id', $pickup->account)->lockForUpdate()->first()
                : null;

            if ($account === null) {
                throw ValidationException::withMessages([
                    'amount' => 'Akun bank sampah untuk pickup ini tidak ditemukan.',
                ]);
            }

            DB::table('transactions')
                ->where('transaction_id', $transaction)
                ->where('transaction_status', self::PAYABLE_STATUS)
                ->update([
                    'amount' => $validated['amount'],
                    'transaction_status' => self::PAID_STATUS,
                    'transaction_date' => now(),
                    'admin' => $request->user()->user_id,
                    'updated_at' => now(),
                ]);

            DB::table('accounts')
                ->where('account_id', $account->account_id)
                ->increment('balance', $validated['amount'], ['updated_at' => now()]);
        });

        return redirect()
            ->route('admin.transaksi.index')
            ->with('status', 'Pembayaran berhasil dicatat dan saldo warga telah diperbarui.');
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', Rule::in([self::PAYABLE_STATUS, self::PAID_STATUS, self::CANCELLED_STATUS])],
            'date_from' => ['sometimes', 'nullable', 'date'],
            'date_to' => ['sometimes', 'nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $transactions = $this->filteredTransactionQuery($filters)
            ->orderByDesc('transactions.transaction_date')
            ->orderByDesc('transactions.transaction_id')
            ->get();

        return Pdf::loadView('admin.laporantransaksi', compact('transactions'))
            ->setPaper('a4', 'landscape')
            ->download('laporan-data-transaksi.pdf');
    }

    private function filteredTransactionQuery(array $filters): Builder
    {
        $query = $this->transactionQuery();

        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = $filters['search'];
            $query->where(function (Builder $query) use ($term): void {
                $query->where('transactions.transaction_id', 'like', "%{$term}%")
                    ->orWhere('users.username', 'like', "%{$term}%")
                    ->orWhere('transactions.amount', 'like', "%{$term}%")
                    ->orWhere('transactions.transaction_information', 'like', "%{$term}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('transactions.transaction_status', $filters['status']);
        }

        if (isset($filters['date_from']) && $filters['date_from'] !== '') {
            $query->whereDate('transactions.transaction_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to'] !== '') {
            $query->whereDate('transactions.transaction_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function transactionQuery(): Builder
    {
        return DB::table('transactions')
            ->join('pickups', 'transactions.pickup', '=', 'pickups.pickup_id')
            ->join('accounts', 'pickups.account', '=', 'accounts.account_id')
            ->join('users', 'accounts.user_id', '=', 'users.user_id')
            ->select([
                'transactions.transaction_id',
                'transactions.pickup',
                'transactions.transaction_date',
                'transactions.amount',
                'transactions.transaction_status',
                'transactions.transaction_information',
                'users.username',
                'users.full_name',
            ]);
    }

    private function eligiblePickupQuery(?int $currentPickupId = null): Builder
    {
        return DB::table('pickups')
            ->join('accounts', 'pickups.account', '=', 'accounts.account_id')
            ->join('users', 'accounts.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->leftJoin('categories', 'pickups.category', '=', 'categories.category_id')
            ->where('roles.role_name', 'Warga')
            ->where('pickups.pickup_status', 'Selesai')
            ->whereNotNull('pickups.waste_weight')
            ->where(function (Builder $query) use ($currentPickupId): void {
                $query->whereNotExists(function (Builder $query): void {
                    $query->selectRaw('1')
                        ->from('transactions')
                        ->whereColumn('transactions.pickup', 'pickups.pickup_id');
                });

                if ($currentPickupId !== null) {
                    $query->orWhere('pickups.pickup_id', $currentPickupId);
                }
            })
            ->select([
                'pickups.pickup_id',
                'pickups.waste_weight',
                'pickups.pickup_finished',
                'pickups.updated_at',
                'pickups.created_at',
                'users.username',
                'users.full_name',
                'categories.category_name',
                'categories.category_price',
            ])
            ->selectRaw('COALESCE(pickups.waste_weight * categories.category_price, 0) as calculated_amount')
            ->orderByDesc('pickups.pickup_finished')
            ->orderByDesc('pickups.pickup_id');
    }

    private function ensurePayable(object $transaction): void
    {
        abort_unless($transaction->transaction_status === self::PAYABLE_STATUS, 404);
    }
}
