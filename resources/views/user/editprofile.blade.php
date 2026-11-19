<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e5f3ef">
    <title>Edit Profile - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e5f3ef] font-sans text-[#263a33] antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-[500px] flex-col px-[18px] pb-[30px] pt-[38px]">
        <header class="mb-[16px]">
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo E-Sabilillah">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a class="grid h-[22px] w-[20px] shrink-0 place-items-center" href="{{ route('user.profile.show') }}" aria-label="Kembali ke profile">
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Edit Profile</h1>
            </div>
        </header>

        @if ($errors->any())
            <p class="mb-[10px] rounded-[10px] bg-[#fff1ef] px-[13px] py-[9px] text-[11px] text-[#a63226]" role="alert">
                Periksa kembali data yang dimasukkan.
            </p>
        @endif

        <form class="flex flex-1 flex-col" method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <section aria-labelledby="account-section-title">
                <h2 class="text-[13px] font-semibold leading-[18px] text-[#205d46]" id="account-section-title">Informasi akun</h2>
                <div class="mt-[7px] grid gap-[7px]">
                    <div class="grid gap-[4px]">
                        <label class="text-[11px] font-semibold leading-[15px] text-[#344054]" for="full_name">Nama lengkap</label>
                        <input class="min-h-[41px] rounded-[11px] border border-[#dce5e1] bg-white px-[12px] text-[11px] text-[#344054] focus:border-[#2f8961] focus:outline-none focus:ring-2 focus:ring-[#2f8961]/15" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" maxlength="100" required>
                        @error('full_name') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid gap-[4px]">
                        <label class="text-[11px] font-semibold leading-[15px] text-[#344054]" for="phone_number">Nomor telepon</label>
                        <input class="min-h-[41px] rounded-[11px] border border-[#dce5e1] bg-white px-[12px] text-[11px] text-[#344054] focus:border-[#2f8961] focus:outline-none focus:ring-2 focus:ring-[#2f8961]/15" id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $user->phone_number) }}" maxlength="20" autocomplete="tel">
                        @error('phone_number') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid gap-[4px]">
                        <label class="text-[11px] font-semibold leading-[15px] text-[#344054]" for="email">Email</label>
                        <input class="min-h-[41px] rounded-[11px] border border-[#dce5e1] bg-white px-[12px] text-[11px] text-[#344054] focus:border-[#2f8961] focus:outline-none focus:ring-2 focus:ring-[#2f8961]/15" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="100" autocomplete="email">
                        @error('email') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <hr class="my-[18px] border-0 border-t border-[#d6e5df]">

            <section aria-labelledby="household-section-title">
                <h2 class="text-[13px] font-semibold leading-[18px] text-[#205d46]" id="household-section-title">Informasi rumah tangga</h2>
                <div class="mt-[7px] grid gap-[7px]">
                    <div class="grid gap-[4px]">
                        <label class="text-[11px] font-semibold leading-[15px] text-[#344054]" for="household_name">Nama rumah tangga</label>
                        <input class="min-h-[41px] rounded-[11px] border border-[#dce5e1] bg-white px-[12px] text-[11px] text-[#344054] focus:border-[#2f8961] focus:outline-none focus:ring-2 focus:ring-[#2f8961]/15" id="household_name" name="household_name" value="{{ old('household_name', $user->household_name) }}" maxlength="100">
                        @error('household_name') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid gap-[4px]">
                        <label class="text-[11px] font-semibold leading-[15px] text-[#344054]" for="address">Alamat rumah</label>
                        <input class="min-h-[41px] rounded-[11px] border border-[#dce5e1] bg-white px-[12px] text-[11px] text-[#344054] focus:border-[#2f8961] focus:outline-none focus:ring-2 focus:ring-[#2f8961]/15" id="address" name="address" value="{{ old('address', $user->address) }}" maxlength="255" autocomplete="street-address">
                        @error('address') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid gap-[4px]">
                        <label class="text-[11px] font-semibold leading-[15px] text-[#344054]" for="rt">Kelurahan / wilayah (RT)</label>
                        <input class="min-h-[41px] rounded-[11px] border border-[#dce5e1] bg-white px-[12px] text-[11px] text-[#344054] placeholder:text-[#687386] focus:border-[#2f8961] focus:outline-none focus:ring-2 focus:ring-[#2f8961]/15" id="rt" name="rt" type="text" inputmode="numeric" pattern="[0-9]{2}" maxlength="2" value="{{ old('rt', $user->rt) }}" placeholder="Pilih wilayah layanan" aria-describedby="rt-hint">
                        <span class="text-[9px] text-[#687386]" id="rt-hint">Masukkan kode RT dua digit sesuai data wilayah layanan.</span>
                        @error('rt') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <details class="mt-[10px] rounded-[10px] border border-[#dce5e1] bg-white px-[11px] py-[8px]" @if ($errors->has('profile_photo')) open @endif>
                <summary class="cursor-pointer text-[10px] font-semibold text-[#205d46]">Foto profil (opsional)</summary>
                <div class="mt-[8px] grid gap-[5px]">
                    <label class="text-[10px] text-[#344054]" for="profile_photo">JPG, PNG, atau WebP, maksimal 2 MB</label>
                    <input class="min-h-[36px] rounded-[8px] border border-[#dce5e1] px-[8px] py-[6px] text-[10px]" id="profile_photo" name="profile_photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    @error('profile_photo') <span class="text-[10px] text-[#b42318]">{{ $message }}</span> @enderror
                </div>
            </details>

            <p class="mt-[15px] rounded-[12px] bg-[#e6f4e9] px-[14px] py-[11px] text-[10px] leading-[14px] text-[#205d46]">
                Beberapa data mungkin perlu dikonfirmasi petugas sebelum diubah.
            </p>
            <div class="mt-auto pt-[24px]">
                <button class="min-h-[40px] w-full rounded-[9px] bg-[#2f8961] px-4 text-[13px] font-semibold text-white transition-colors hover:bg-[#205d46] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" type="submit">
                    Simpan Perubahan
                </button>
                <p class="mt-[16px] text-center text-[10px] leading-[14px] text-[#7b8794]">Periksa kembali data sebelum menyimpan.</p>
            </div>
        </form>
    </main>
</body>
</html>