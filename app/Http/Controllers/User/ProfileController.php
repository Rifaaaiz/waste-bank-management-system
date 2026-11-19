<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $this->citizen($request);

        return view('user.profile', [
            'user' => $user,
            'avatarUrl' => $user->profile_photo !== null
                && Storage::disk('public')->exists($user->profile_photo)
                    ? Storage::disk('public')->url($user->profile_photo)
                    : null,
            'avatarInitial' => mb_strtoupper(mb_substr($user->full_name ?: $user->username, 0, 1)),
            'maskedPhone' => $this->maskPhone($user->phone_number),
            'publicUserId' => 'USR-'.str_pad((string) $user->user_id, 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('user.editprofile', [
            'user' => $this->citizen($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->citizen($request);
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'household_name' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'regex:/^[0-9]{2}$/D'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        unset($validated['profile_photo']);

        $previousPhoto = $user->profile_photo;
        $newPhoto = $request->file('profile_photo')?->storePublicly(
            'profile-photos/warga/'.$user->user_id,
            'public'
        );

        $user->fill($validated)->save();

        if ($newPhoto !== null) {
            $user->profile_photo = $newPhoto;
            $user->save();
        }

        if ($newPhoto !== null
            && $previousPhoto !== null
            && str_starts_with($previousPhoto, 'profile-photos/warga/'.$user->user_id.'/')) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()
            ->route('user.profile.show')
            ->with('status', 'Profil berhasil diperbarui.');
    }

    public function settings(Request $request): View
    {
        $user = $this->citizen($request);

        return view('user.pengaturanakun', [
            'user' => $user,
            'avatarInitial' => mb_strtoupper(mb_substr($user->full_name ?: $user->username, 0, 1)),
            'maskedPhone' => $this->maskPhone($user->phone_number),
        ]);
    }

    public function help(Request $request): View
    {
        $this->citizen($request);

        return view('user.bantuan');
    }

    private function citizen(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->role?->role_name === 'Warga', 403);

        return $user;
    }

    private function maskPhone(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return 'Belum diisi';
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) < 8) {
            return $phone;
        }

        return substr($digits, 0, 4).' •••• '.substr($digits, -4);
    }
}
