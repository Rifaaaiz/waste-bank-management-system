<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->citizen($request);
        $today = now()->toDateString();
        $items = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (object $notification): array => $this->formatNotification($notification));

        $todayItems = $items
            ->filter(fn (array $notification): bool => $notification['date_key'] === $today)
            ->values();

        $olderGroups = $items
            ->reject(fn (array $notification): bool => $notification['date_key'] === $today)
            ->groupBy(fn (array $notification): string => $notification['date_key'] ?? 'unknown')
            ->map(function (Collection $notifications, string $date): array {
                $groupDate = $date === 'unknown' ? null : Carbon::parse($date)->locale('id');

                return [
                    'label' => $groupDate?->translatedFormat('d M Y') ?? 'Tanggal tidak tersedia',
                    'is_today' => false,
                    'items' => $notifications->values(),
                ];
            })
            ->values();

        $groups = collect([[
            'label' => 'Hari ini',
            'is_today' => true,
            'items' => $todayItems,
        ]])->concat($olderGroups)->values();

        return view('user.notifikasi', [
            'groups' => $groups,
            'selectedNotification' => null,
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = $this->citizen($request);

        $updated = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('user.notifications.index')
            ->with('status', $updated > 0
                ? 'Semua notifikasi sudah ditandai dibaca.'
                : 'Tidak ada notifikasi baru.');
    }

    public function show(Request $request, int $notification): View
    {
        $user = $this->citizen($request);
        $record = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->where('notification_id', $notification)
            ->firstOrFail();

        if (! $record->is_read) {
            DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->where('notification_id', $notification)
                ->update([
                    'is_read' => true,
                    'updated_at' => now(),
                ]);
        }

        $createdAt = $record->created_at
            ? Carbon::parse($record->created_at)->locale('id')
            : null;

        return view('user.notifikasi', [
            'groups' => collect(),
            'selectedNotification' => [
                'title' => $record->title,
                'message' => $record->message,
                'type' => $record->notification_type,
                'date' => $createdAt?->translatedFormat('d M Y, H.i') ?? 'Tanggal tidak tersedia',
                'reference' => $record->withdrawal_id
                    ? 'Pencairan #'.$record->withdrawal_id
                    : ($record->transaction_id
                        ? 'Transaksi #'.$record->transaction_id
                        : ($record->pickup_id ? 'Pengajuan #'.$record->pickup_id : null)),
                'is_read' => true,
            ],
        ]);
    }

    private function citizen(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->role?->role_name === 'Warga', 403);

        return $user;
    }

    private function formatNotification(object $notification): array
    {
        $createdAt = $notification->created_at
            ? Carbon::parse($notification->created_at)->locale('id')
            : null;

        return [
            'id' => (int) $notification->notification_id,
            'title' => $notification->title,
            'message' => $notification->message,
            'date_key' => $createdAt?->toDateString(),
            'date_label' => $createdAt?->translatedFormat('d M') ?? 'Tanggal tidak tersedia',
            'is_read' => (bool) $notification->is_read,
        ];
    }
}
