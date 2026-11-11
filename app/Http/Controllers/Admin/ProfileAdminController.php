<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileAdminController extends Controller
{
    public function show(Request $request): View
    {
        $admin = $this->authenticatedAdmin($request);
        $admin->load('role');

        return view('admin.profileadmin', [
            'admin' => $admin,
            'avatarUrl' => $admin->profile_photo !== null
                && Storage::disk('public')->exists($admin->profile_photo)
                    ? route('admin.profile.photo')
                    : asset('images/oranghitam.png'),
        ]);
    }

    public function photo(Request $request): StreamedResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $disk = Storage::disk('public');
        $path = $admin->profile_photo;

        abort_unless($path !== null && $disk->exists($path), 404);
        $mimeType = mime_content_type($disk->path($path));
        abort_unless(in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        return response()->stream(function () use ($disk, $path): void {
            $stream = $disk->readStream($path);

            if ($stream === false) {
                throw new \RuntimeException('Unable to read the admin profile photo.');
            }

            try {
                fpassthru($stream);
            } finally {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s()-]{7,19}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $previousPhoto = $admin->profile_photo;
        $newPhoto = $request->file('profile_photo')?->storePublicly(
            'profile-photos/admin/'.$admin->user_id,
            'public'
        );

        $admin->full_name = $validated['full_name'];
        $admin->phone_number = $validated['phone_number'] ?? null;
        $admin->address = $validated['address'] ?? null;

        if ($newPhoto !== null) {
            $admin->profile_photo = $newPhoto;
        }

        $admin->save();

        if ($newPhoto !== null && $this->isManagedPhoto($previousPhoto, $admin->user_id)) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()
            ->route('admin.profile.show')
            ->with('status', 'Profil admin berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', 'different:current_password'],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password_hash)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $admin->forceFill([
            'password_hash' => Hash::make($validated['password']),
        ])->save();

        return redirect()
            ->route('admin.profile.show')
            ->with('status', 'Password berhasil diubah.');
    }

    private function authenticatedAdmin(Request $request): User
    {
        $admin = $request->user();
        abort_unless($admin instanceof User && $admin->role?->role_name === 'Admin', 403);

        return $admin;
    }

    private function isManagedPhoto(?string $path, int $userId): bool
    {
        return $path !== null && str_starts_with($path, 'profile-photos/admin/'.$userId.'/');
    }
}
