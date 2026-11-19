<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('user.registeruser');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'address' => ['required', 'string', 'max:255'],
            'rt' => ['required', 'regex:/^[0-9]{2}$/D'],
        ]);

        $memberRoleId = Role::query()
            ->where('role_name', 'Warga')
            ->value('role_id');

        abort_if($memberRoleId === null, 503, 'Pendaftaran belum tersedia karena role Warga belum dikonfigurasi.');

        DB::transaction(function () use ($validated, $memberRoleId): void {
            $user = User::create([
                'username' => $validated['username'],
                'phone_number' => $validated['phone_number'],
                'password_hash' => Hash::make($validated['password']),
                'address' => $validated['address'],
                'rt' => $validated['rt'],
                'role_id' => $memberRoleId,
            ]);

            if (Schema::hasTable('accounts') && ! Account::query()->where('user_id', $user->user_id)->exists()) {
                Account::query()->create([
                    'user_id' => $user->user_id,
                    'balance' => 0,
                ]);
            }
        });

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil. Silakan masuk dengan username dan password Anda.');
    }
}
