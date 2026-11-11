@extends('admin.layouts.warga')

@section('title', 'Profile Admin')

@section('styles')
    <style>
        .profile-page { width: 100%; }
        .profile-card { width: 100%; margin: 55px auto; padding: 46px; border-radius: 12px; background: #fff; }
        .profile-identity { display: flex; align-items: flex-start; gap: 20px; }
        .profile-photo { display: block; width: 190px; height: 200px; flex: 0 0 auto; border-radius: 8px; background: #edf3ef; object-fit: cover; }
        .profile-identity-copy { padding-top: 9px; }
        .profile-name { margin: 0 0 5px; color: #141a16; font-size: clamp(24px, 3vw, 32px); font-weight: 700; line-height: 1.2; overflow-wrap: anywhere; }
        .profile-role { margin: 0; color: #303833; font-size: 16px; }
        .profile-details { display: grid; gap: 18px; margin-top: 20px; }
        .profile-detail { min-width: 0; }
        .profile-detail h2 { margin: 0 0 2px; color: #171d19; font-size: 20px; font-weight: 700; line-height: 1.4; }
        .profile-detail p { margin: 0; color: #252b27; font-size: 16px; line-height: 1.5; overflow-wrap: anywhere; white-space: pre-line; }
        .profile-password-mask { letter-spacing: 1px; }
        .profile-notice { margin-bottom: 18px; padding: 12px 15px; border-radius: 8px; background: #e9f6ee; color: #286744; font-size: 13px; }
        .profile-notice.error { background: #fff1ef; color: #a63226; }
        .profile-tools { display: grid; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #e8eeea; }
        .profile-tool { border: 1px solid #e0e9e3; border-radius: 9px; background: #fff; }
        .profile-tool summary { padding: 12px 15px; color: #2f7656; font-size: 14px; font-weight: 650; cursor: pointer; }
        .profile-form { display: grid; gap: 13px; padding: 0 15px 16px; }
        .profile-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .profile-field { display: grid; min-width: 0; gap: 6px; }
        .profile-field.full-width { grid-column: 1 / -1; }
        .profile-field label { color: #34443b; font-size: 12px; font-weight: 600; }
        .profile-field input, .profile-field textarea { width: 100%; min-height: 42px; padding: 9px 11px; border: 1px solid #dce5df; border-radius: 7px; outline: 0; background: #fff; color: #26372e; font-size: 13px; }
        .profile-field textarea { min-height: 75px; resize: vertical; }
        .profile-field input:focus, .profile-field textarea:focus { border-color: #2F8961; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
        .profile-field input[type="file"] { min-height: auto; padding: 8px; }
        .field-error { color: #bd3527; font-size: 12px; }
        .profile-submit { justify-self: start; min-height: 39px; padding: 0 15px; border: 0; border-radius: 7px; background: #2F8961; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; }
        @media (max-width: 900px) { .admin-main { padding: 24px; } .profile-card { margin: 38px auto; padding: 34px; } }
        @media (max-width: 600px) {
            .admin-main { padding: 15px; }
            .profile-card { margin: 8px auto 20px; padding: 24px 18px; }
            .profile-identity { align-items: center; flex-direction: column; gap: 14px; text-align: center; }
            .profile-photo { width: min(190px, 65vw); height: min(200px, 68vw); }
            .profile-identity-copy { padding-top: 0; }
            .profile-name { font-size: 26px; }
            .profile-role { font-size: 14px; }
            .profile-details { gap: 15px; margin-top: 24px; }
            .profile-detail h2 { font-size: 19px; }
            .profile-detail p { font-size: 15px; }
            .profile-tools { margin-top: 23px; }
            .profile-form-grid { grid-template-columns: 1fr; }
            .profile-field.full-width { grid-column: auto; }
        }
    </style>
@endsection

@section('content')
    <div class="profile-page">
        <section class="profile-card" aria-label="Profil admin">
            @if (session('status'))
                <div class="profile-notice" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="profile-notice error" role="alert">Periksa kembali data yang dimasukkan pada formulir profil.</div>
            @endif

            <header class="profile-identity">
                <img class="profile-photo" src="{{ $avatarUrl }}" alt="Foto profil {{ $admin->full_name ?: $admin->username }}">
                <div class="profile-identity-copy">
                    <h1 class="profile-name">{{ $admin->full_name ?: $admin->username }}</h1>
                    <p class="profile-role">{{ $admin->position ?: ($admin->role?->role_name ?? 'Admin') }}</p>
                </div>
            </header>

            <div class="profile-details">
                <section class="profile-detail">
                    <h2>Nomor Telepon</h2>
                    <p>{{ $admin->phone_number ?: 'Belum diisi' }}</p>
                </section>
                <section class="profile-detail">
                    <h2>Alamat</h2>
                    <p>{{ $admin->address ?: 'Belum diisi' }}</p>
                </section>
                <section class="profile-detail">
                    <h2>Password</h2>
                    <p class="profile-password-mask" aria-label="Password disembunyikan">••••••••••••••••</p>
                </section>
            </div>

            <div class="profile-tools">
                <details class="profile-tool" @if ($errors->hasAny(['full_name', 'phone_number', 'address', 'profile_photo'])) open @endif>
                    <summary>Edit Profil</summary>
                    <form class="profile-form" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="profile-form-grid">
                            <div class="profile-field">
                                <label for="full_name">Nama lengkap</label>
                                <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $admin->full_name) }}" maxlength="100" required>
                                @error('full_name') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="profile-field">
                                <label for="phone_number">Nomor telepon</label>
                                <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $admin->phone_number) }}" maxlength="20" autocomplete="tel">
                                @error('phone_number') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="profile-field full-width">
                                <label for="address">Alamat</label>
                                <textarea id="address" name="address" maxlength="255">{{ old('address', $admin->address) }}</textarea>
                                @error('address') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="profile-field full-width">
                                <label for="profile_photo">Foto profil (JPG, PNG, atau WebP, maksimal 2 MB)</label>
                                <input id="profile_photo" name="profile_photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                @error('profile_photo') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <button class="profile-submit" type="submit">Simpan Profil</button>
                    </form>
                </details>

                <details class="profile-tool" @if ($errors->hasAny(['current_password', 'password', 'password_confirmation'])) open @endif>
                    <summary>Ubah Password</summary>
                    <form class="profile-form" method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="profile-form-grid">
                            <div class="profile-field full-width">
                                <label for="current_password">Password saat ini</label>
                                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                                @error('current_password') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="profile-field">
                                <label for="password">Password baru</label>
                                <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                                @error('password') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="profile-field">
                                <label for="password_confirmation">Konfirmasi password baru</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
                            </div>
                        </div>
                        <button class="profile-submit" type="submit">Simpan Password</button>
                    </form>
                </details>
            </div>
        </section>
    </div>
@endsection