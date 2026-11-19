<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            $roleName = Auth::user()->role?->role_name;

            if ($roleName === 'Admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($roleName === 'Warga') {
                return redirect()->route('user.dashboard');
            }
        }

        return view('user.loginuser');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user?->role?->role_name === 'Admin') {
            return redirect()->route('admin.dashboard')->with('status', 'Login berhasil.');
        }

        return redirect()->route('user.dashboard')->with('status', 'Login berhasil.');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role?->role_name === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role?->role_name !== 'Warga') {
            Auth::logout();

            return redirect()->route('login')->withErrors(['username' => 'Akses belum diizinkan.']);
        }

        $account = Schema::hasTable('accounts')
            ? DB::table('accounts')->where('user_id', $user->user_id)->first()
            : null;
        $balance = (float) ($account->balance ?? 0);
        $accountId = $account === null ? null : (int) $account->account_id;
        $activities = $this->recentActivities($accountId);
        $validated = $request->validate([
            'month' => ['sometimes', 'date_format:Y-m'],
        ]);
        $period = (isset($validated['month'])
            ? Carbon::createFromFormat('!Y-m', $validated['month'])
            : Carbon::now())
            ->locale('id')
            ->startOfMonth();
        $statistics = $this->statistics($accountId, $balance, $period);

        return view('user.berandauser', [
            'user' => $user,
            'balance' => $balance,
            'activities' => $activities,
            'statistics' => $statistics,
            'period' => $period,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function recentActivities(?int $accountId): array
    {
        if ($accountId === null || ! Schema::hasTable('pickups')) {
            return [];
        }

        $activities = collect();
        $hasCategories = Schema::hasTable('categories');
        $hasTransactions = Schema::hasTable('transactions');

        $pickups = DB::table('pickups')
            ->when($hasCategories, function ($query): void {
                $query->leftJoin('categories', 'pickups.category', '=', 'categories.category_id');
            })
            ->where('pickups.account', $accountId)
            ->when($hasTransactions, function ($query): void {
                $query->whereNotExists(function ($transactionQuery): void {
                    $transactionQuery->selectRaw('1')
                        ->from('transactions')
                        ->whereColumn('transactions.pickup', 'pickups.pickup_id');
                });
            })
            ->orderByDesc('pickups.created_at')
            ->limit(10)
            ->get([
                'pickups.pickup_id as id',
                'pickups.pickup_status as status',
                'pickups.waste_weight as weight',
                'pickups.pickup_type as pickup_type',
                'pickups.created_at as event_date',
                'pickups.pickup_finished',
                'pickups.pickup_schedule',
                ...($hasCategories ? ['categories.category_name'] : []),
            ]);

        foreach ($pickups as $pickup) {
            $category = $pickup->category_name ?? null;
            $date = $pickup->pickup_finished ?? $pickup->pickup_schedule ?? $pickup->event_date;
            $weight = $pickup->weight === null
                ? null
                : rtrim(rtrim(number_format((float) $pickup->weight, 2, ',', '.'), '0'), ',').' kg';

            $activities->push([
                'id' => 'pickup-'.$pickup->id,
                'kind' => 'pickup',
                'title' => $category ? 'Penjualan '.$category : 'Pengajuan sampah',
                'description' => trim(implode(' · ', array_filter([
                    $weight,
                    'Pengajuan Sampah',
                ]))),
                'date' => $this->formatActivityDate($date),
                'status' => $pickup->status,
                'sort' => Carbon::parse($date ?? now())->getTimestamp(),
            ]);
        }

        if ($hasTransactions) {
            $transactions = DB::table('transactions')
                ->join('pickups', 'transactions.pickup', '=', 'pickups.pickup_id')
                ->when($hasCategories, function ($query): void {
                    $query->leftJoin('categories', 'pickups.category', '=', 'categories.category_id');
                })
                ->where('pickups.account', $accountId)
                ->orderByDesc('transactions.transaction_date')
                ->limit(10)
                ->get([
                    'transactions.transaction_id as id',
                    'transactions.transaction_status as status',
                    'transactions.transaction_information',
                    'transactions.transaction_date as event_date',
                    'pickups.waste_weight as weight',
                    ...($hasCategories ? ['categories.category_name'] : []),
                ]);

            foreach ($transactions as $transaction) {
                $category = $transaction->category_name ?? null;
                $activities->push([
                    'id' => 'transaction-'.$transaction->id,
                    'kind' => 'transaction',
                    'title' => $transaction->status === 'Selesai' ? 'Dana masuk' : 'Transaksi penjualan sampah',
                    'description' => $transaction->transaction_information
                        ?: ($category ? 'Hasil penjualan '.$category : 'Transaksi bank sampah'),
                    'date' => $this->formatActivityDate($transaction->event_date),
                    'status' => $transaction->status,
                    'sort' => Carbon::parse($transaction->event_date ?? now())->getTimestamp(),
                ]);
            }
        }

        return $activities
            ->sortByDesc('sort')
            ->take(4)
            ->map(function (array $activity): array {
                unset($activity['sort']);

                return $activity;
            })
            ->values()
            ->all();
    }

    private function statistics(?int $accountId, float $balance, Carbon $period): array
    {
        $categories = collect();
        $totalWeight = 0.0;

        if ($accountId !== null && Schema::hasTable('pickups') && Schema::hasTable('categories')) {
            $categories = DB::table('pickups')
                ->join('categories', 'pickups.category', '=', 'categories.category_id')
                ->where('pickups.account', $accountId)
                ->whereBetween('pickups.created_at', [
                    $period->copy()->startOfMonth(),
                    $period->copy()->endOfMonth(),
                ])
                ->select('categories.category_id', 'categories.category_name')
                ->selectRaw('SUM(COALESCE(pickups.waste_weight, 0)) as item_count')
                ->groupBy('categories.category_id', 'categories.category_name')
                ->orderByDesc('item_count')
                ->get()
                ->map(function (object $category, int $index): array {
                    $colors = ['#8fc31f', '#737de8', '#293b78', '#9c3030', '#1d4fe0', '#8437f6'];
                    $name = mb_strtolower($category->category_name);
                    $icon = match (true) {
                        str_contains($name, 'plastik') => 'Botolhijau-icon.png',
                        str_contains($name, 'kertas') => 'Buku_icon.png',
                        str_contains($name, 'kardus') => 'Kotak-icon.png',
                        str_contains($name, 'kaca') => 'Botol-icon.png',
                        str_contains($name, 'besi') => 'Magnet-icon.png',
                        default => 'Kategori-icon.png',
                    };

                    return [
                        'name' => $category->category_name,
                        'count' => (float) $category->item_count,
                        'color' => $colors[$index % count($colors)],
                        'icon' => $icon,
                    ];
                });

            $totalWeight = (float) $categories->sum('count');
        }

        return [
            'balance' => $balance,
            'total_weight' => $totalWeight,
            'categories' => $categories->all(),
            'chart' => $this->categoryChart($categories->all(), $totalWeight),
            'period_start' => $period->copy()->locale('id')->startOfMonth()->translatedFormat('j M Y'),
            'period_end' => $period->copy()->locale('id')->endOfMonth()->translatedFormat('j M Y'),
            'previous_month' => $period->copy()->subMonthNoOverflow()->format('Y-m'),
            'next_month' => $period->copy()->addMonthNoOverflow()->format('Y-m'),
            'has_next_month' => $period->copy()->addMonthNoOverflow()->lte(Carbon::now()->startOfMonth()),
        ];
    }

    private function categoryChart(array $categories, float $totalWeight): string
    {
        if ($totalWeight <= 0 || $categories === []) {
            return 'conic-gradient(#e5eee9 0deg 360deg)';
        }

        $gradient = [];
        $degrees = 0.0;

        foreach ($categories as $index => $category) {
            $end = $index === array_key_last($categories)
                ? 360
                : $degrees + ($category['count'] / $totalWeight * 360);
            $gradient[] = $category['color'].' '.$degrees.'deg '.$end.'deg';
            $degrees = $end;
        }

        return 'conic-gradient('.implode(', ', $gradient).')';
    }

    private function formatActivityDate(?string $date): string
    {
        if ($date === null) {
            return 'Tanggal tidak tersedia';
        }

        $timestamp = Carbon::parse($date)->locale('id');

        if ($timestamp->isToday()) {
            return 'Hari ini, '.$timestamp->format('H.i');
        }

        if ($timestamp->isYesterday()) {
            return 'Kemarin, '.$timestamp->format('H.i');
        }

        return $timestamp->translatedFormat('d M Y, H.i');
    }
}
