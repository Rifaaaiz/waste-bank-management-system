<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminPasswordResetNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AdminPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('admin.lupapasswordadmin', [
            'emailReset' => $request->query('method') === 'email',
        ]);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
        ]);

        $adminRoleId = Role::query()
            ->where('role_name', 'Admin')
            ->value('role_id');

        if ($adminRoleId !== null) {
            Password::broker()->sendResetLink(
                [...$validated, 'role_id' => $adminRoleId],
                function (CanResetPassword $user, string $token): string {
                    if (! $user instanceof User || $user->role?->role_name !== 'Admin') {
                        Password::broker()->deleteToken($user);

                        return Password::RESET_LINK_SENT;
                    }

                    $user->notify(new AdminPasswordResetNotification($token));

                    return Password::RESET_LINK_SENT;
                }
            );
        }

        return back()->with(
            'status',
            'Jika username dan email cocok dengan akun admin, tautan reset akan dikirim ke email tersebut.'
        );
    }

    public function edit(Request $request, string $token): View|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->whereHas('role', fn ($query) => $query->where('role_name', 'Admin'))
            ->first();

        if (! $user || ! Password::broker()->tokenExists($user, $token)) {
            return redirect()
                ->route('admin.password.request')
                ->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa.']);
        }

        return view('admin.lupapasswordadmin', [
            'token' => $token,
            'email' => $user->email,
            'username' => $user->username,
        ]);
    }

    public function updateWithCurrentPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'current_password' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', 'different:current_password'],
        ]);

        $user = User::query()
            ->where('username', $validated['username'])
            ->whereHas('role', fn ($query) => $query->where('role_name', 'Admin'))
            ->first();

        if (! $user || ! Hash::check($validated['current_password'], $user->password_hash)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['current_password' => 'Username atau password lama admin tidak valid.']);
        }

        DB::transaction(function () use ($user, $validated): void {
            $user->forceFill([
                'password_hash' => Hash::make($validated['password']),
            ])->save();

            event(new PasswordReset($user));
        });

        return redirect()
            ->route('admin.login')
            ->with('status', 'Password berhasil diubah. Username admin tetap sama.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
            'token' => ['required', 'string'],
            'current_password' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', 'different:current_password'],
        ]);

        $adminRoleId = Role::query()
            ->where('role_name', 'Admin')
            ->value('role_id');

        $user = User::query()
            ->where('username', $validated['username'])
            ->where('email', $validated['email'])
            ->whereHas('role', fn ($query) => $query->where('role_name', 'Admin'))
            ->first();

        if (! $user || ! Hash::check($validated['current_password'], $user->password_hash)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['current_password' => 'Username atau password lama admin tidak valid.']);
        }

        $status = $adminRoleId === null
            ? Password::INVALID_USER
            : DB::transaction(fn (): string => Password::broker()->reset(
                [
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'token' => $validated['token'],
                    'password' => $validated['password'],
                    'role_id' => $adminRoleId,
                ],
                function (User $user, string $password): void {
                    if ($user->role?->role_name !== 'Admin') {
                        abort(403);
                    }

                    $user->forceFill([
                        'password_hash' => Hash::make($password),
                    ])->save();

                    event(new PasswordReset($user));
                }
            ));

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa.']);
        }

        return redirect()
            ->route('admin.login')
            ->with('status', 'Password berhasil diubah. Silakan masuk dengan password baru.');
    }
}
