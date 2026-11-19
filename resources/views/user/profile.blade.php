<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#205d46">
    <title>Profile - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e5f3ef] font-sans text-[#111827] antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[500px] bg-[#e5f3ef]">
        <header class="rounded-b-[13px] bg-[#205d46] px-[18px] pb-[18px] pt-[38px] text-white">
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo E-Sabilillah">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a class="grid h-[22px] w-[20px] shrink-0 place-items-center" href="{{ route('user.dashboard') }}" aria-label="Kembali ke beranda">
                    <img class="h-[16px] w-[16px] brightness-0 invert" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px]">Profile</h1>
            </div>
            <div class="mt-[25px] flex items-center gap-[14px]">
                @if ($avatarUrl)
                    <img class="h-[66px] w-[66px] shrink-0 rounded-full object-cover" src="{{ $avatarUrl }}" alt="Foto profil {{ $user->full_name ?: $user->username }}">
                @else
                    <span class="grid h-[66px] w-[66px] shrink-0 place-items-center rounded-full bg-[#d7eee3] text-[28px] font-bold text-[#205d46]" aria-label="Inisial {{ $user->full_name ?: $user->username }}">
                        {{ $avatarInitial }}
                    </span>
                @endif
                <div class="min-w-0">
                    <p class="truncate text-[16px] font-bold leading-[20px]">{{ $user->full_name ?: $user->username }}</p>
                    <p class="mt-[4px] text-[11px] leading-[15px] text-white/80">Pengguna E-Sabilillah</p>
                </div>
            </div>
        </header>

        <div class="grid gap-[8px] px-[18px] pb-[30px] pt-[21px]">
            @if (session('status'))
                <p class="rounded-[12px] bg-white px-[14px] py-[10px] text-[11px] font-medium text-[#205d46]" role="status">{{ session('status') }}</p>
            @endif

            <section class="rounded-[15px] border border-[#dce5e1] bg-white px-[16px] py-[15px]" aria-labelledby="account-info-title">
                <h2 class="text-[12px] font-bold leading-[16px] text-black" id="account-info-title">Informasi Akun</h2>
                <dl class="mt-[10px] grid gap-[10px] text-[10px] leading-[14px]">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#687386]">Nomor telepon</dt>
                        <dd class="text-right font-medium text-black">{{ $maskedPhone }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#687386]">Email</dt>
                        <dd class="max-w-[65%] break-all text-right font-medium text-black">{{ $user->email ?: 'Belum diisi' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[#687386]">ID Pengguna</dt>
                        <dd class="text-right font-medium text-black">{{ $publicUserId }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-[15px] border border-[#dce5e1] bg-white px-[16px] py-[15px]" aria-labelledby="household-info-title">
                <h2 class="text-[13px] font-bold leading-[17px] text-black" id="household-info-title">Informasi Rumah Tangga</h2>
                <dl class="mt-[10px] grid gap-[10px] text-[12px] leading-[16px]">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[#687386]">Nama rumah tangga</dt>
                        <dd class="max-w-[58%] text-right font-medium text-black">{{ $user->household_name ?: 'Belum diisi' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-[#687386]">Alamat</dt>
                        <dd class="max-w-[68%] text-right font-medium text-black">{{ $user->address ?: 'Belum diisi' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="mt-[8px]" aria-labelledby="settings-title">
                <h2 class="mb-[6px] text-[13px] font-semibold leading-[17px] text-black" id="settings-title">Pengaturan</h2>
                <nav class="grid gap-[7px]" aria-label="Pengaturan profil">
                    <a class="flex min-h-[30px] items-center gap-[10px] rounded-[9px] border border-[#dce5e1] bg-white px-[10px] text-[9px] font-medium text-black transition-colors hover:bg-[#f8fbf9] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" href="{{ route('user.profile.edit') }}">
                        <img class="h-[15px] w-[15px] object-contain" src="{{ asset('icons/Edittable-icon.png') }}" alt="">
                        <span class="flex-1">Edit Profil</span>
                        <img class="h-[13px] w-[13px] rotate-180 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                    </a>
                    <a class="flex min-h-[30px] items-center gap-[10px] rounded-[9px] border border-[#dce5e1] bg-white px-[10px] text-[9px] font-medium text-black transition-colors hover:bg-[#f8fbf9] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" href="{{ route('user.settings') }}">
                        <img class="h-[15px] w-[15px] object-contain" src="{{ asset('icons/settings-icon.png') }}" alt="">
                        <span class="flex-1">Pengaturan</span>
                        <img class="h-[13px] w-[13px] rotate-180 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                    </a>
                    <a class="flex min-h-[30px] items-center gap-[10px] rounded-[9px] border border-[#dce5e1] bg-white px-[10px] text-[9px] font-medium text-black transition-colors hover:bg-[#f8fbf9] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" href="{{ route('user.help') }}">
                        <img class="h-[15px] w-[15px] object-contain" src="{{ asset('icons/Bantuan-icon.png') }}" alt="">
                        <span class="flex-1">Bantuan</span>
                        <img class="h-[13px] w-[13px] rotate-180 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex min-h-[30px] w-full items-center gap-[10px] rounded-[9px] border border-[#dce5e1] bg-white px-[10px] text-left text-[9px] font-medium text-[#c42424] transition-colors hover:bg-[#fffafa] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#c42424]" type="submit">
                            <img class="h-[15px] w-[15px] object-contain [filter:brightness(0)_saturate(100%)_invert(14%)_sepia(89%)_saturate(5253%)_hue-rotate(353deg)_brightness(93%)_contrast(118%)]" src="{{ asset('icons/logout-icon.png') }}" alt="">
                            <span class="flex-1">Keluar</span>
                        </button>
                    </form>
                </nav>
            </section>
        </div>
    </main>
</body>
</html>