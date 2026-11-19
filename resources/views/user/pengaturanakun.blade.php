<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e5f3ef">
    <title>Pengaturan Akun - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e5f3ef] font-sans text-[#263a33] antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[500px] px-[18px] pb-[24px] pt-[38px]">
        <header>
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo E-Sabilillah">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a class="grid h-[22px] w-[20px] shrink-0 place-items-center" href="{{ route('user.profile.show') }}" aria-label="Kembali ke profile">
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Pengaturan Akun</h1>
            </div>
        </header>

        <section class="mt-[20px] flex items-center gap-[12px]" aria-label="Informasi pengguna">
            <span class="grid h-[42px] w-[42px] shrink-0 place-items-center rounded-full bg-[#d7eee3] text-[18px] font-bold text-[#205d46]" aria-hidden="true">{{ $avatarInitial }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-[13px] font-semibold leading-[18px] text-[#344054]">{{ $user->full_name ?: $user->username }}</p>
                <p class="mt-[2px] truncate text-[10px] leading-[14px] text-[#7b8794]">{{ $user->email ?: 'Email belum diisi' }}</p>
            </div>
            <span class="shrink-0 text-[10px] font-semibold text-[#2f8961]">Akun aktif</span>
        </section>

        <section class="mt-[25px]" aria-labelledby="security-title">
            <h2 class="mb-[3px] text-[13px] font-semibold leading-[18px] text-[#205d46]" id="security-title">Keamanan akun</h2>
            <a class="flex min-h-[58px] items-center gap-[12px] border-b border-[#d6e5df] py-[9px]" href="{{ route('password.request') }}">
                <svg class="h-[18px] w-[18px] shrink-0 text-[#2f8961]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h1Zm2 0h6V7a3 3 0 0 0-6 0v3Zm3 4a1.5 1.5 0 0 0-1 2.62V19h2v-2.38A1.5 1.5 0 0 0 12 14Z"/>
                </svg>
                <span class="min-w-0 flex-1">
                    <span class="block text-[11px] font-medium leading-[15px] text-[#344054]">Ubah kata sandi</span>
                    <span class="mt-[3px] block text-[10px] leading-[14px] text-[#7b8794]">Jaga keamanan akses akun Anda</span>
                </span>
                <img class="h-[13px] w-[13px] rotate-180 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
            </a>
            <a class="flex min-h-[58px] items-center gap-[12px] border-b border-[#d6e5df] py-[9px]" href="{{ route('user.profile.edit') }}#phone_number">
                <svg class="h-[18px] w-[18px] shrink-0 text-[#2f8961]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="6.5" y="2.5" width="11" height="19" rx="2"/><path d="M10 5h4M11 18.5h2"/>
                </svg>
                <span class="min-w-0 flex-1">
                    <span class="block text-[11px] font-medium leading-[15px] text-[#344054]">Nomor telepon</span>
                    <span class="mt-[3px] block text-[10px] leading-[14px] text-[#7b8794]">{{ $maskedPhone }}</span>
                </span>
                <img class="h-[13px] w-[13px] rotate-180 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
            </a>
            <a class="flex min-h-[58px] items-center gap-[12px] border-b border-[#d6e5df] py-[9px]" href="{{ route('user.profile.edit') }}#email">
                <svg class="h-[18px] w-[18px] shrink-0 text-[#2f8961]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>
                </svg>
                <span class="min-w-0 flex-1">
                    <span class="block text-[11px] font-medium leading-[15px] text-[#344054]">Email</span>
                    <span class="mt-[3px] block truncate text-[10px] leading-[14px] text-[#7b8794]">{{ $user->email ?: 'Belum diisi' }}</span>
                </span>
                <img class="h-[13px] w-[13px] rotate-180 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
            </a>
        </section>

        <section class="mt-[22px] border-b border-[#d6e5df] pb-[12px]" aria-labelledby="notifications-title">
            <h2 class="mb-[6px] text-[13px] font-semibold leading-[18px] text-[#205d46]" id="notifications-title">Notifikasi</h2>
            <div class="flex min-h-[48px] items-center gap-[12px]">
                <span class="min-w-0 flex-1">
                    <span class="block text-[11px] font-medium leading-[15px] text-[#344054]">Pembaruan pengajuan</span>
                    <span class="mt-[3px] block text-[10px] leading-[14px] text-[#7b8794]">Perubahan status pengajuan sampah</span>
                </span>
                <span class="relative inline-flex h-[22px] w-[38px] shrink-0 cursor-not-allowed items-center rounded-full bg-[#d0d5dd]" role="switch" aria-checked="false" aria-disabled="true" aria-label="Pembaruan pengajuan, belum tersedia">
                    <span class="absolute left-[3px] h-[16px] w-[16px] rounded-full bg-white shadow-sm"></span>
                </span>
            </div>
            <p class="mt-[2px] text-[9px] leading-[13px] text-[#7b8794]">Preferensi notifikasi belum tersedia.</p>
        </section>

        <section class="mt-[22px]" aria-labelledby="privacy-title">
            <h2 class="mb-[3px] text-[13px] font-semibold leading-[18px] text-[#205d46]" id="privacy-title">Privasi &amp; ketentuan</h2>
            <div class="flex min-h-[50px] items-center gap-[12px] border-b border-[#d6e5df] py-[8px]" aria-label="Kebijakan privasi belum tersedia">
                <svg class="h-[18px] w-[18px] shrink-0 text-[#2f8961]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 3 20 6v5c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-3Z"/><path d="M12 8v5m0 3h.01"/>
                </svg>
                <span class="min-w-0 flex-1 text-[11px] font-medium text-[#344054]">Kebijakan privasi</span>
                <span class="text-[9px] text-[#7b8794]">Belum tersedia</span>
            </div>
            <div class="flex min-h-[50px] items-center gap-[12px] border-b border-[#d6e5df] py-[8px]" aria-label="Syarat dan ketentuan belum tersedia">
                <svg class="h-[18px] w-[18px] shrink-0 text-[#2f8961]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v5h5M9 13h7m-7 4h7"/>
                </svg>
                <span class="min-w-0 flex-1 text-[11px] font-medium text-[#344054]">Syarat dan ketentuan</span>
                <span class="text-[9px] text-[#7b8794]">Belum tersedia</span>
            </div>
        </section>

        <form class="mt-[12px]" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex min-h-[42px] w-full items-center gap-[12px] text-left text-[11px] font-medium text-[#c42424]" type="submit">
                <img class="h-[15px] w-[15px] object-contain [filter:brightness(0)_saturate(100%)_invert(14%)_sepia(89%)_saturate(5253%)_hue-rotate(353deg)_brightness(93%)_contrast(118%)]" src="{{ asset('icons/logout-icon.png') }}" alt="">
                <span>Keluar</span>
            </button>
        </form>
        <p class="mt-[3px] text-center text-[9px] leading-[14px] text-[#7b8794]">Anda dapat masuk kembali dengan akun yang sama.</p>
    </main>
</body>
</html>
