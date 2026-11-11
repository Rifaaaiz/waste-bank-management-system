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
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DataPetugasController extends Controller
{
    private const PAGE_SIZE = 10;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $petugas = $this->filteredPetugasQuery($filters)
            ->orderBy('full_name')
            ->orderBy('user_id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('admin.datapetugas', [
            'petugas' => $petugas,
            'positions' => $this->petugasQuery()
                ->whereNotNull('position')
                ->distinct()
                ->orderBy('position')
                ->pluck('position'),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.tambahdatapetugas');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'position' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        $petugasRoleId = $this->petugasRoleId();

        User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'phone_number' => $validated['phone_number'],
            'position' => $validated['position'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $petugasRoleId,
        ]);

        return redirect()
            ->route('admin.petugas.index')
            ->with('status', 'Data petugas berhasil ditambahkan.');
    }

    public function edit(User $petugas): View
    {
        return view('admin.editdatapetugas', [
            'petugas' => $this->petugasQuery()->findOrFail($petugas->user_id),
        ]);
    }

    public function update(Request $request, User $petugas): RedirectResponse
    {
        $petugas = $this->petugasQuery()->findOrFail($petugas->user_id);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($petugas->user_id, 'user_id')],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'position' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        DB::transaction(function () use ($petugas, $validated): void {
            $petugas->fill([
                'full_name' => $validated['full_name'],
                'username' => $validated['username'],
                'phone_number' => $validated['phone_number'],
                'position' => $validated['position'],
            ]);

            if (! empty($validated['password'])) {
                $petugas->password_hash = Hash::make($validated['password']);
            }

            $petugas->save();
        });

        return redirect()
            ->route('admin.petugas.index')
            ->with('status', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(User $petugas): RedirectResponse
    {
        $petugas = $this->petugasQuery()->findOrFail($petugas->user_id);

        if (Schema::hasTable('pickups')
            && Schema::hasColumn('pickups', 'officer')
            && DB::table('pickups')->where('officer', $petugas->user_id)->exists()) {
            return back()->withErrors([
                'delete' => 'Petugas sudah terhubung dengan riwayat penjemputan dan tidak dapat dihapus.',
            ]);
        }

        $petugas->delete();

        return redirect()
            ->route('admin.petugas.index')
            ->with('status', 'Data petugas berhasil dihapus.');
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $petugas = $this->filteredPetugasQuery($filters)
            ->orderBy('full_name')
            ->orderBy('user_id')
            ->get(['user_id', 'username', 'phone_number', 'full_name', 'position']);

        return Pdf::loadView('admin.laporanpetugas', compact('petugas'))
            ->setPaper('a4', 'landscape')
            ->download('laporan-data-petugas.pdf');
    }

    private function filteredPetugasQuery(array $filters): Builder
    {
        $query = $this->petugasQuery();

        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = $filters['search'];
            $query->where(function (Builder $query) use ($term): void {
                $query->where('user_id', 'like', "%{$term}%")
                    ->orWhere('username', 'like', "%{$term}%")
                    ->orWhere('phone_number', 'like', "%{$term}%")
                    ->orWhere('full_name', 'like', "%{$term}%")
                    ->orWhere('position', 'like', "%{$term}%");
            });
        }

        if (isset($filters['position']) && $filters['position'] !== '') {
            $query->where('position', $filters['position']);
        }

        return $query;
    }

    private function petugasQuery(): Builder
    {
        $roleId = Role::query()->where('role_name', 'Petugas')->value('role_id');

        return User::query()->when(
            $roleId === null,
            fn (Builder $query) => $query->whereRaw('1 = 0'),
            fn (Builder $query) => $query->where('role_id', $roleId)
        );
    }

    private function petugasRoleId(): int
    {
        $roleId = Role::query()->where('role_name', 'Petugas')->value('role_id');

        abort_if($roleId === null, 503, 'Role Petugas belum dikonfigurasi.');

        return (int) $roleId;
    }
}
