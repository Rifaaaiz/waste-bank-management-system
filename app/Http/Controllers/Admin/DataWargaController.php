<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DataWargaController extends Controller
{
    private const PAGE_SIZE = 10;

    private const RT_OPTIONS = ['01', '02', '03', '04', '05', '06', '07'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'rt' => ['sometimes', 'nullable', 'regex:/^[0-9]{2}$/D'],
            'blok_rumah' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $wargas = $this->filteredWargaQuery($filters)
            ->orderBy('full_name')
            ->orderBy('user_id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('admin.datawarga', [
            'wargas' => $wargas,
            'rts' => $this->wargaQuery()->whereNotNull('rt')->distinct()->orderBy('rt')->pluck('rt'),
            'blokRumahs' => $this->wargaQuery()->whereNotNull('blok_rumah')->distinct()->orderBy('blok_rumah')->pluck('blok_rumah'),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.tambahdatawarga', [
            'rtOptions' => self::RT_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'address' => ['required', 'string', 'max:255'],
            'blok_rumah' => ['required', 'string', 'max:100'],
            'rt' => ['required', Rule::in(self::RT_OPTIONS)],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        $wargaRoleId = $this->wargaRoleId();

        DB::transaction(function () use ($validated, $wargaRoleId): void {
            User::create([
                'full_name' => $validated['full_name'],
                'username' => $validated['username'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'blok_rumah' => $validated['blok_rumah'],
                'rt' => $validated['rt'],
                'password_hash' => Hash::make($validated['password']),
                'role_id' => $wargaRoleId,
            ]);
        });

        return redirect()
            ->route('admin.warga.index')
            ->with('status', 'Data warga berhasil ditambahkan.');
    }

    public function edit(User $warga): View
    {
        $warga = $this->wargaQuery()->findOrFail($warga->user_id);

        return view('admin.editdatawarga', [
            'warga' => $warga,
            'rtOptions' => self::RT_OPTIONS,
        ]);
    }

    public function update(Request $request, User $warga): RedirectResponse
    {
        $warga = $this->wargaQuery()->findOrFail($warga->user_id);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($warga->user_id, 'user_id')],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'address' => ['required', 'string', 'max:255'],
            'blok_rumah' => ['required', 'string', 'max:100'],
            'rt' => ['required', Rule::in(self::RT_OPTIONS)],
            'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        DB::transaction(function () use ($warga, $validated): void {
            $warga->fill([
                'full_name' => $validated['full_name'],
                'username' => $validated['username'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'blok_rumah' => $validated['blok_rumah'],
                'rt' => $validated['rt'],
            ]);

            if (! empty($validated['password'])) {
                $warga->password_hash = Hash::make($validated['password']);
            }

            $warga->save();
        });

        return redirect()
            ->route('admin.warga.index')
            ->with('status', 'Data warga berhasil diperbarui.');
    }

    public function destroy(User $warga): RedirectResponse
    {
        $warga = $this->wargaQuery()->findOrFail($warga->user_id);

        if (DB::table('accounts')->where('user_id', $warga->user_id)->exists()) {
            return back()->withErrors([
                'delete' => 'Data warga memiliki akun bank sampah dan tidak dapat dihapus agar riwayat transaksi tetap aman.',
            ]);
        }

        $warga->delete();

        return redirect()
            ->route('admin.warga.index')
            ->with('status', 'Data warga berhasil dihapus.');
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'rt' => ['sometimes', 'nullable', 'regex:/^[0-9]{2}$/D'],
            'blok_rumah' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $wargas = $this->filteredWargaQuery($filters)
            ->orderBy('full_name')
            ->orderBy('user_id')
            ->get([
                'user_id',
                'full_name',
                'username',
                'phone_number',
                'address',
                'blok_rumah',
                'rt',
            ]);

        return Pdf::loadView('admin.laporanwarga', compact('wargas'))
            ->setPaper('a4', 'landscape')
            ->download('laporan-data-warga.pdf');
    }

    private function filteredWargaQuery(array $filters): Builder
    {
        $query = $this->wargaQuery();

        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = $filters['search'];
            $query->where(function (Builder $query) use ($term): void {
                $query->where('full_name', 'like', "%{$term}%")
                    ->orWhere('username', 'like', "%{$term}%")
                    ->orWhere('phone_number', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%")
                    ->orWhere('blok_rumah', 'like', "%{$term}%")
                    ->orWhere('rt', 'like', "%{$term}%");
            });
        }

        if (isset($filters['rt']) && $filters['rt'] !== '') {
            $query->where('rt', $filters['rt']);
        }

        if (isset($filters['blok_rumah']) && $filters['blok_rumah'] !== '') {
            $query->where('blok_rumah', $filters['blok_rumah']);
        }

        return $query;
    }

    private function wargaQuery(): Builder
    {
        $roleId = Role::query()->where('role_name', 'Warga')->value('role_id');

        return User::query()->when(
            $roleId === null,
            fn (Builder $query) => $query->whereRaw('1 = 0'),
            fn (Builder $query) => $query->where('role_id', $roleId)
        );
    }

    private function wargaRoleId(): int
    {
        $roleId = Role::query()->where('role_name', 'Warga')->value('role_id');

        abort_if($roleId === null, 503, 'Role Warga belum dikonfigurasi.');

        return (int) $roleId;
    }
}
