<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    private const PAGE_SIZE = 5;

    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'between:2000,2100'],
        ]);

        $period = CarbonImmutable::create(
            (int) ($validated['year'] ?? now()->year),
            (int) ($validated['month'] ?? now()->month),
            1
        );
        $startOfMonth = $period->startOfMonth();
        $endOfMonth = $period->endOfMonth();

        $citizenRoleId = Role::query()
            ->where('role_name', 'Warga')
            ->value('role_id');

        $totalCitizens = $citizenRoleId === null
            ? 0
            : DB::table('users')->where('role_id', $citizenRoleId)->count();

        $totalTransactions = DB::table('transactions')->count();
        $totalWasteWeight = $this->completedWasteQuery()->sum('pickups.waste_weight');

        $transactions = DB::table('transactions')
            ->join('pickups', 'transactions.pickup', '=', 'pickups.pickup_id')
            ->join('accounts', 'pickups.account', '=', 'accounts.account_id')
            ->join('users', 'accounts.user_id', '=', 'users.user_id')
            ->select([
                'transactions.transaction_id',
                'transactions.transaction_date',
                'transactions.amount',
                'transactions.transaction_status',
                'users.full_name',
                'users.username',
            ])
            ->orderByDesc('transactions.transaction_date')
            ->orderByDesc('transactions.transaction_id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        $monthlyCategories = DB::table('categories')
            ->join('pickups', function ($join) use ($startOfMonth, $endOfMonth): void {
                $join->on('pickups.category', '=', 'categories.category_id')
                    ->where('pickups.pickup_status', '=', 'Selesai')
                    ->whereNotNull('pickups.waste_weight')
                    ->whereRaw(
                        'COALESCE(pickups.pickup_finished, pickups.updated_at, pickups.created_at) BETWEEN ? AND ?',
                        [$startOfMonth, $endOfMonth]
                    )
                    ->whereNotExists(function (Builder $query): void {
                        $query->selectRaw('1')
                            ->from('transactions')
                            ->whereColumn('transactions.pickup', 'pickups.pickup_id')
                            ->where('transactions.transaction_status', 'Dibatalkan');
                    });
            })
            ->select([
                'categories.category_id',
                'categories.category_name',
            ])
            ->selectRaw('COALESCE(SUM(pickups.waste_weight), 0) as total_weight')
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderBy('categories.category_name')
            ->get();

        $monthlyCategories = $monthlyCategories->map(function (object $category): object {
            $category->total_weight = (float) $category->total_weight;

            return $category;
        });

        $activities = $this->recentActivities();

        return view('admin.dashboard', [
            'admin' => $request->user(),
            'totalCitizens' => $totalCitizens,
            'totalTransactions' => $totalTransactions,
            'totalWasteWeight' => (float) $totalWasteWeight,
            'transactions' => $transactions,
            'monthlyCategories' => $monthlyCategories,
            'period' => $period,
            'previousPeriod' => $period->subMonth(),
            'nextPeriod' => $period->addMonth(),
            'activities' => $activities,
        ]);
    }

    private function completedWasteQuery(): Builder
    {
        return DB::table('pickups')
            ->where('pickups.pickup_status', 'Selesai')
            ->whereNotNull('pickups.waste_weight')
            ->whereNotExists(function (Builder $query): void {
                $query->selectRaw('1')
                    ->from('transactions')
                    ->whereColumn('transactions.pickup', 'pickups.pickup_id')
                    ->where('transactions.transaction_status', 'Dibatalkan');
            });
    }

    private function recentActivities(): Collection
    {
        $notifications = DB::table('notifications')
            ->select([
                'title',
                'message',
                'notification_type',
                'created_at',
                'is_read',
                'pickup_id',
                'transaction_id',
                'withdrawal_id',
            ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn (object $item): array => [
                'title' => $item->title,
                'detail' => $item->message,
                'type' => $item->notification_type,
                'status' => (bool) $item->is_read ? 'Dibaca' : 'Baru',
                'reference' => $item->pickup_id
                    ? 'Pickup #'.$item->pickup_id
                    : ($item->transaction_id
                        ? 'Transaksi #'.$item->transaction_id
                        : ($item->withdrawal_id ? 'Pencairan #'.$item->withdrawal_id : null)),
                'created_at' => $item->created_at,
            ]);

        $pickupActivities = DB::table('pickup_status_histories')
            ->join('pickups', 'pickup_status_histories.pickup_id', '=', 'pickups.pickup_id')
            ->select([
                'pickup_status_histories.status as title',
                'pickup_status_histories.description as detail',
                'pickups.pickup_status as type',
                'pickup_status_histories.created_at',
                'pickup_status_histories.pickup_id',
            ])
            ->orderByDesc('pickup_status_histories.created_at')
            ->limit(6)
            ->get()
            ->map(fn (object $item): array => [
                'title' => 'Status pengajuan: '.$item->title,
                'detail' => $item->detail,
                'type' => $item->type,
                'status' => $item->type,
                'reference' => 'Pickup #'.$item->pickup_id,
                'created_at' => $item->created_at,
            ]);

        $withdrawalActivities = DB::table('withdrawals')
            ->select([
                'withdrawal_id',
                'withdrawal_status',
                'withdrawal_method',
                'created_at',
            ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn (object $item): array => [
                'title' => 'Permintaan pencairan',
                'detail' => 'Metode: '.$item->withdrawal_method,
                'type' => $item->withdrawal_status,
                'status' => $item->withdrawal_status,
                'reference' => 'Pencairan #'.$item->withdrawal_id,
                'created_at' => $item->created_at,
            ]);

        return $notifications
            ->concat($pickupActivities)
            ->concat($withdrawalActivities)
            ->sortByDesc('created_at')
            ->take(6)
            ->values();
    }
}
