<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        if ($user->role?->role_name === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role?->role_name !== 'Warga') {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'username' => 'Akses belum diizinkan.',
            ]);
        }

        $accountIds = Schema::hasTable('accounts')
            ? DB::table('accounts')->where('user_id', $user->user_id)->pluck('account_id')
            : collect();
        $entries = collect();
        $hasCategories = Schema::hasTable('categories');

        if ($accountIds->isNotEmpty() && Schema::hasTable('pickups')) {
            $pickups = DB::table('pickups')
                ->when($hasCategories, function ($query): void {
                    $query->leftJoin('categories', 'pickups.category', '=', 'categories.category_id');
                })
                ->whereIn('pickups.account', $accountIds)
                ->orderByDesc('pickups.created_at')
                ->get([
                    'pickups.pickup_id as id',
                    'pickups.pickup_status as status',
                    'pickups.waste_weight as weight',
                    'pickups.pickup_finished',
                    'pickups.pickup_schedule',
                    'pickups.created_at as event_date',
                    ...($hasCategories ? ['categories.category_name'] : []),
                ]);

            foreach ($pickups as $pickup) {
                $category = $pickup->category_name ?? null;
                $date = $pickup->pickup_finished ?? $pickup->pickup_schedule ?? $pickup->event_date;
                $status = (string) $pickup->status;
                $weight = $this->formatWeight($pickup->weight);

                $entries->push([
                    'id' => 'pickup-'.$pickup->id,
                    'kind' => 'pickup',
                    'title' => $category ? 'Penjualan '.$category : 'Pengajuan Sampah',
                    'description' => implode(' · ', array_filter([$weight, 'Pengajuan Sampah'])),
                    'date' => $this->formatDate($date),
                    'sort' => $date ? Carbon::parse($date)->getTimestamp() : 0,
                    'status' => $status,
                    'status_tone' => $this->statusTone($status),
                    'filter_status' => $this->filterStatus($status),
                ]);
            }
        }

        if ($accountIds->isNotEmpty()
            && Schema::hasTable('pickups')
            && Schema::hasTable('transactions')) {
            $transactions = DB::table('transactions')
                ->join('pickups', 'transactions.pickup', '=', 'pickups.pickup_id')
                ->when($hasCategories, function ($query): void {
                    $query->leftJoin('categories', 'pickups.category', '=', 'categories.category_id');
                })
                ->whereIn('pickups.account', $accountIds)
                ->orderByDesc('transactions.transaction_date')
                ->get([
                    'transactions.transaction_id as id',
                    'transactions.amount',
                    'transactions.transaction_status as status',
                    'transactions.transaction_information',
                    'transactions.transaction_date as event_date',
                    'transactions.created_at',
                    ...($hasCategories ? ['categories.category_name'] : []),
                ]);

            foreach ($transactions as $transaction) {
                $category = $transaction->category_name ?? null;
                $date = $transaction->event_date ?? $transaction->created_at;
                $status = (string) $transaction->status;
                $isPaid = strtolower(trim($status)) === 'selesai';

                $entries->push([
                    'id' => 'transaction-'.$transaction->id,
                    'kind' => 'transaction',
                    'title' => $isPaid ? 'Dana masuk' : 'Transaksi bank sampah',
                    'description' => $transaction->transaction_information
                        ?: ($category ? 'Hasil Penjualan '.$category : 'Transaksi bank sampah'),
                    'date' => $this->formatDate($date),
                    'sort' => $date ? Carbon::parse($date)->getTimestamp() : 0,
                    'status' => $status,
                    'status_tone' => $this->statusTone($status),
                    'filter_status' => $this->filterStatus($status),
                    'amount' => ($isPaid ? '+ Rp ' : 'Rp ')
                        .number_format((float) $transaction->amount, 0, ',', '.'),
                ]);
            }
        }

        return view('user.riwayat', [
            'entries' => $entries->sortByDesc('sort')->values(),
        ]);
    }

    private function formatWeight(?float $weight): ?string
    {
        if ($weight === null) {
            return null;
        }

        return rtrim(rtrim(number_format($weight, 2, ',', '.'), '0'), ',').' kg';
    }

    private function formatDate(?string $date): string
    {
        if ($date === null) {
            return 'Tanggal tidak tersedia';
        }

        $timestamp = Carbon::parse($date)->locale('id');

        return $timestamp->isToday()
            ? 'Hari ini, '.$timestamp->format('H.i')
            : $timestamp->translatedFormat('d M Y, H.i');
    }

    private function filterStatus(string $status): string
    {
        return match (strtolower(trim($status))) {
            'menunggu' => 'menunggu',
            'selesai', 'disetujui' => 'selesai',
            'ditolak' => 'ditolak',
            default => 'lainnya',
        };
    }

    private function statusTone(string $status): string
    {
        return match (strtolower(trim($status))) {
            'disetujui', 'selesai' => 'success',
            'menunggu' => 'pending',
            'ditolak' => 'rejected',
            default => 'neutral',
        };
    }
}
