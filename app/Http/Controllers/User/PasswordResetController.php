<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('user.lupapassworduser');
    }

    public function emailResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
        ]);

        Password::sendResetLink($validated);

        return back()
            ->withInput($request->only('username', 'email'))
            ->with('status', 'Jika username dan email cocok dengan akun, tautan reset akan dikirim ke email tersebut.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('user.lupapassworduser', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('username', 'email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()
            ->route('password.request')
            ->with('status', 'Password berhasil diubah. Silakan masuk dengan password baru.');
    }
}
