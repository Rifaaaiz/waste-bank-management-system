@php($editing = isset($petugas))

<style>
    .petugas-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .petugas-form-field { display: grid; min-width: 0; align-content: start; gap: 7px; }
    .petugas-form-field-wide { grid-column: 1 / -1; }
    .petugas-form-field label { color: #30453a; font-size: 13px; font-weight: 600; }
    .petugas-form-field input { width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #d5e0da; border-radius: 7px; outline: none; background: #fff; color: #25352c; }
    .petugas-form-field input:focus { border-color: #2F8961; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
    .petugas-form-field input::placeholder { color: #9aa79f; }
    .petugas-form-error { color: #bd3527; font-size: 12px; }
    .petugas-form-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
    .petugas-form-button { display: inline-flex; min-height: 44px; align-items: center; justify-content: center; padding: 0 17px; border: 0; border-radius: 7px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; }
    .petugas-form-button.primary { background: #07805b; color: #fff; }
    .petugas-form-button.secondary { background: #edf3ef; color: #40564a; }
    @media (max-width: 620px) { .petugas-form-grid { grid-template-columns: 1fr; gap: 14px; } .petugas-form-field-wide { grid-column: auto; } }
</style>

<form method="POST" action="{{ $editing ? route('admin.petugas.update', $petugas->user_id) : route('admin.petugas.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="petugas-form-grid">
        <div class="petugas-form-field petugas-form-field-wide">
            <label for="full_name">Nama Petugas</label>
            <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $editing ? $petugas->full_name : '') }}" placeholder="Masukan Nama Petugas" maxlength="100" required>
            @error('full_name') <span class="petugas-form-error">{{ $message }}</span> @enderror
        </div>
        <div class="petugas-form-field">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username', $editing ? $petugas->username : '') }}" placeholder="Masukan Username" maxlength="50" autocomplete="username" required>
            @error('username') <span class="petugas-form-error">{{ $message }}</span> @enderror
        </div>
        <div class="petugas-form-field">
            <label for="phone_number">Nomor Telepon</label>
            <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $editing ? $petugas->phone_number : '') }}" placeholder="Masukan Nomor Telepon" maxlength="20" required>
            @error('phone_number') <span class="petugas-form-error">{{ $message }}</span> @enderror
        </div>
        <div class="petugas-form-field petugas-form-field-wide">
            <label for="position">Jabatan</label>
            <input id="position" name="position" type="text" value="{{ old('position', $editing ? $petugas->position : '') }}" placeholder="Masukan Jabatan" maxlength="100" required>
            @error('position') <span class="petugas-form-error">{{ $message }}</span> @enderror
        </div>
        <div class="petugas-form-field">
            <label for="password">{{ $editing ? 'Password baru (opsional)' : 'Password' }}</label>
            <input id="password" name="password" type="password" placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Masukan Password' }}" autocomplete="new-password" minlength="8" maxlength="255" {{ $editing ? '' : 'required' }}>
            @error('password') <span class="petugas-form-error">{{ $message }}</span> @enderror
        </div>
        <div class="petugas-form-field">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Konfirmasi Password" autocomplete="new-password" minlength="8" maxlength="255" {{ $editing ? '' : 'required' }}>
        </div>
    </div>

    <div class="petugas-form-actions">
        <button class="petugas-form-button primary" type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah Petugas' }}</button>
        <a class="petugas-form-button secondary" href="{{ route('admin.petugas.index') }}">Batal</a>
    </div>
</form>
