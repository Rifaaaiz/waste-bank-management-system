<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FinanceController extends Controller
{
    private const COMPLETED_STATUS = 'Selesai';

    private const RESERVED_WITHDRAWAL_STATUSES = [
        'Menunggu Konfirmasi',
        'Diproses',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $user = $this->citizen($request);
        $filters = $request->validate([
            'month' => ['sometimes', 'date_format:Y-m'],
        ]);
        $period = Carbon::parse(($filters['month'] ?? now()->format('Y-m')).'-01')
            ->startOfMonth()
            ->locale('id');
        $account = Schema::hasTable('accounts')
            ? DB::table('accounts')->where('user_id', $user->user_id)->first()
            : null;
        $accountId = $account?->account_id;
        $balance = (float) ($account->balance ?? 0);
        $availableBalance = $this->availableBalance($accountId, $balance);

        return view('user.keuangan', [
            'balance' => $balance,
            'totalIncome' => $this->totalIncome($accountId),
            'totalWithdrawals' => $this->totalWithdrawals($accountId),
            'availableBalance' => $availableBalance,
            'entries' => $this->monthlyEntries($accountId, $period),
            'period' => $period,
            'previousMonth' => $period->copy()->subMonthNoOverflow()->format('Y-m'),
            'nextMonth' => $period->copy()->addMonthNoOverflow()->format('Y-m'),
            'hasNextMonth' => $period->copy()->addMonthNoOverflow()->lte(now()->startOfMonth()),
        ]);
    }

    public function createWithdrawal(Request $request): View|RedirectResponse
    {
        $user = $this->citizen($request);
        $account = Schema::hasTable('accounts')
            ? DB::table('accounts')->where('user_id', $user->user_id)->first()
            : null;

        return view('user.ajukanpencairan', [
            'availableBalance' => $this->availableBalance(
                $account?->account_id,
                (float) ($account->balance ?? 0)
            ),
        ]);
    }

    public function storeWithdrawal(Request $request): RedirectResponse
    {
        $user = $this->citizen($request);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'withdrawal_method' => [
                'required',
                'string',
                Rule::in(['Datang langsung', 'ShopeePay', 'DANA', 'BCA Mobile']),
            ],
        ]);

        abort_unless(
            Schema::hasTable('accounts') && Schema::hasTable('withdrawals'),
            503,
            'Data keuangan belum tersedia.'
        );

        DB::transaction(function () use ($validated, $user): void {
            $account = DB::table('accounts')
                ->where('user_id', $user->user_id)
                ->lockForUpdate()
                ->first();

            if ($account === null) {
                throw ValidationException::withMessages([
                    'amount' => 'Akun keuangan belum tersedia.',
                ]);
            }

            $reservedAmount = (float) DB::table('withdrawals')
                ->where('account_id', $account->account_id)
                ->whereIn('withdrawal_status', self::RESERVED_WITHDRAWAL_STATUSES)
                ->sum('amount');
            $availableAmount = max(
                0,
                $this->toMinorUnits($account->balance) - $this->toMinorUnits($reservedAmount)
            );

            if ($this->toMinorUnits($validated['amount']) > $availableAmount) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal pencairan melebihi saldo yang tersedia.',
                ]);
            }

            DB::table('withdrawals')->insert([
                'account_id' => $account->account_id,
                'amount' => $validated['amount'],
                'withdrawal_method' => $validated['withdrawal_method'],
                'destination_name' => null,
                'destination_number' => null,
                'withdrawal_status' => 'Menunggu Konfirmasi',
                'processed_by' => null,
                'withdrawal_note' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('user.keuangan')
            ->with('status', 'Pengajuan pencairan berhasil dibuat dan menunggu konfirmasi.');
    }

    private function citizen(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->role?->role_name === 'Warga', 403);

        return $user;
    }

    private function totalIncome(?int $accountId): float
    {
        if ($accountId === null
            || ! Schema::hasTable('transactions')
            || ! Schema::hasTable('pickups')) {
            return 0;
        }

        return (float) DB::table('transactions')
            ->join('pickups', 'transactions.pickup', '=', 'pickups.pickup_id')
            ->where('pickups.account', $accountId)
            ->where('transactions.transaction_status', self::COMPLETED_STATUS)
            ->sum('transactions.amount');
    }

    private function totalWithdrawals(?int $accountId): float
    {
        if ($accountId === null || ! Schema::hasTable('withdrawals')) {
            return 0;
        }

        return (float) DB::table('withdrawals')
            ->where('account_id', $accountId)
            ->where('withdrawal_status', self::COMPLETED_STATUS)
            ->sum('amount');
    }

    private function reservedWithdrawals(?int $accountId): float
    {
        if ($accountId === null || ! Schema::hasTable('withdrawals')) {
            return 0;
        }

        return (float) DB::table('withdrawals')
            ->where('account_id', $accountId)
            ->whereIn('withdrawal_status', self::RESERVED_WITHDRAWAL_STATUSES)
            ->sum('amount');
    }

    private function availableBalance(?int $accountId, float $balance): float
    {
        $reservedBalance = $this->reservedWithdrawals($accountId);

        return max(
            0,
            $this->toMinorUnits($balance) - $this->toMinorUnits($reservedBalance)
        ) / 100;
    }

    private function monthlyEntries(?int $accountId, Carbon $period): Collection
    {
        $entries = collect();

        if ($accountId === null) {
            return $entries;
        }

        if (Schema::hasTable('transactions') && Schema::hasTable('pickups')) {
            $hasCategories = Schema::hasTable('categories');
            $transactions = DB::table('transactions')
                ->join('pickups', 'transactions.pickup', '=', 'pickups.pickup_id')
                ->when($hasCategories, function ($query): void {
                    $query->leftJoin('categories', 'pickups.category', '=', 'categories.category_id');
                })
                ->where('pickups.account', $accountId)
                ->where('transactions.transaction_status', self::COMPLETED_STATUS)
                ->whereBetween('transactions.transaction_date', [
                    $period->copy()->startOfMonth(),
                    $period->copy()->endOfMonth(),
                ])
                ->get([
                    'transactions.transaction_id',
                    'transactions.amount',
                    'transactions.transaction_information',
                    'transactions.transaction_date',
                    ...($hasCategories ? ['categories.category_name'] : []),
                ]);

            foreach ($transactions as $transaction) {
                $date = $transaction->transaction_date;
                $category = $transaction->category_name ?? null;

                $entries->push([
                    'kind' => 'income',
                    'title' => $category ? 'Penjualan '.$category : 'Dana masuk',
                    'description' => $transaction->transaction_information
                        ?: ($category ? 'Hasil penjualan '.$category : 'Hasil penjualan sampah'),
                    'date' => $this->formatDate($date),
                    'sort' => $date ? Carbon::parse($date)->getTimestamp() : 0,
                    'amount' => '+ Rp '.$this->formatMoney($transaction->amount),
                    'status' => self::COMPLETED_STATUS,
                    'status_tone' => 'success',
                ]);
            }
        }

        if (Schema::hasTable('withdrawals')) {
            $withdrawals = DB::table('withdrawals')
                ->where('account_id', $accountId)
                ->whereBetween('updated_at', [
                    $period->copy()->startOfMonth(),
                    $period->copy()->endOfMonth(),
                ])
                ->get([
                    'withdrawal_id',
                    'amount',
                    'withdrawal_method',
                    'withdrawal_status',
                    'updated_at',
                    'created_at',
                ]);

            foreach ($withdrawals as $withdrawal) {
                $date = $withdrawal->updated_at ?? $withdrawal->created_at;
                $isCompleted = $withdrawal->withdrawal_status === self::COMPLETED_STATUS;

                $entries->push([
                    'kind' => 'withdrawal',
                    'title' => 'Pencairan saldo',
                    'description' => 'Metode: '.$withdrawal->withdrawal_method,
                    'date' => $this->formatDate($date),
                    'sort' => $date ? Carbon::parse($date)->getTimestamp() : 0,
                    'amount' => ($isCompleted ? '- Rp ' : 'Rp ').$this->formatMoney($withdrawal->amount),
                    'status' => $withdrawal->withdrawal_status,
                    'status_tone' => $this->withdrawalStatusTone($withdrawal->withdrawal_status),
                ]);
            }
        }

        return $entries->sortByDesc('sort')->values();
    }

    private function formatDate(?string $date): string
    {
        return $date
            ? Carbon::parse($date)->locale('id')->translatedFormat('d M Y, H.i')
            : 'Tanggal tidak tersedia';
    }

    private function formatMoney(float|string $amount): string
    {
        return number_format((float) $amount, 0, ',', '.');
    }

    private function toMinorUnits(float|string $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function withdrawalStatusTone(string $status): string
    {
        return match ($status) {
            'Selesai' => 'success',
            'Menunggu Konfirmasi', 'Diproses' => 'pending',
            'Ditolak', 'Dibatalkan' => 'rejected',
            default => 'neutral',
        };
    }
}
