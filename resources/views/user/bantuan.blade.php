<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e5f3ef">
    <title>Bantuan - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e5f3ef] font-sans text-[#263a33] antialiased">
    <main class="mx-auto flex min-h-screen min-h-[100svh] w-full max-w-[500px] flex-col px-[18px] pb-[18px] pt-[38px]">
        <header>
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo E-Sabilillah">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a class="grid h-[22px] w-[20px] shrink-0 place-items-center" href="{{ route('user.profile.show') }}" data-back-link aria-label="Kembali ke profile">
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Bantuan</h1>
            </div>
        </header>

        <section class="mt-[16px]" aria-labelledby="help-intro-title">
            <h2 class="text-[16px] font-bold leading-[22px] text-[#344054]" id="help-intro-title">Ada yang bisa kami bantu?</h2>
            <p class="mt-[5px] text-[11px] leading-[17px] text-[#75818f]">Temukan jawaban seputar pengajuan sampah, status, dan pencairan dana.</p>
        </section>

        <section class="mt-[25px]" aria-labelledby="faq-title">
            <h2 class="mb-[5px] text-[13px] font-bold leading-[18px] text-[#205d46]" id="faq-title">Pertanyaan umum</h2>
            <div class="divide-y divide-[#d4e5df]">
                <article class="py-[12px]">
                    <h3>
                        <button class="flex min-h-[18px] w-full items-center justify-between gap-[12px] text-left text-[11px] font-semibold leading-[18px] text-[#344054] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" type="button" aria-expanded="false" aria-controls="faq-answer-1" data-faq-toggle>
                            <span>Bagaimana cara menjual sampah?</span>
                            <span class="hidden shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-open>−</span>
                            <span class="shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-closed>+</span>
                        </button>
                    </h3>
                    <div class="grid grid-rows-[0fr] opacity-0 transition-[grid-template-rows,opacity] duration-300 ease-out" id="faq-answer-1" aria-hidden="true" data-faq-panel>
                        <div class="min-h-0 overflow-hidden">
                            <p class="pt-[7px] text-[10px] leading-[17px] text-[#75818f]">Buka menu Jual, lengkapi formulir, lalu pilih Kirim Pengajuan. Setelah terkirim, halaman berhasil akan muncul. Tidak ada perkiraan nilai sampah.</p>
                        </div>
                    </div>
                </article>
                <article class="py-[12px]">
                    <h3>
                        <button class="flex min-h-[18px] w-full items-center justify-between gap-[12px] text-left text-[11px] font-semibold leading-[18px] text-[#344054] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" type="button" aria-expanded="false" aria-controls="faq-answer-2" data-faq-toggle>
                            <span>Di mana saya melihat status pengajuan?</span>
                            <span class="hidden shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-open>−</span>
                            <span class="shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-closed>+</span>
                        </button>
                    </h3>
                    <div class="grid grid-rows-[0fr] opacity-0 transition-[grid-template-rows,opacity] duration-300 ease-out" id="faq-answer-2" aria-hidden="true" data-faq-panel>
                        <div class="min-h-0 overflow-hidden">
                            <p class="pt-[7px] text-[10px] leading-[17px] text-[#75818f]">Buka Riwayat, lalu pilih pengajuan untuk melihat status dan perkembangannya di Detail Pengajuan.</p>
                        </div>
                    </div>
                </article>
                <article class="py-[12px]">
                    <h3>
                        <button class="flex min-h-[18px] w-full items-center justify-between gap-[12px] text-left text-[11px] font-semibold leading-[18px] text-[#344054] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" type="button" aria-expanded="false" aria-controls="faq-answer-3" data-faq-toggle>
                            <span>Bagaimana cara mencairkan dana?</span>
                            <span class="hidden shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-open>−</span>
                            <span class="shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-closed>+</span>
                        </button>
                    </h3>
                    <div class="grid grid-rows-[0fr] opacity-0 transition-[grid-template-rows,opacity] duration-300 ease-out" id="faq-answer-3" aria-hidden="true" data-faq-panel>
                        <div class="min-h-0 overflow-hidden">
                            <p class="pt-[7px] text-[10px] leading-[17px] text-[#75818f]">Buka menu Dana dan ajukan pencairan. Saat ini hanya tersedia Datang langsung setelah konfirmasi petugas. Transfer bank dan dompet digital akan tersedia di masa depan.</p>
                        </div>
                    </div>
                </article>
                <article class="py-[12px]">
                    <h3>
                        <button class="flex min-h-[18px] w-full items-center justify-between gap-[12px] text-left text-[11px] font-semibold leading-[18px] text-[#344054] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961]" type="button" aria-expanded="false" aria-controls="faq-answer-4" data-faq-toggle>
                            <span>Bagaimana cara mengubah data profil?</span>
                            <span class="hidden shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-open>−</span>
                            <span class="shrink-0 text-[16px] font-normal leading-none text-[#2f8961]" aria-hidden="true" data-icon-closed>+</span>
                        </button>
                    </h3>
                    <div class="grid grid-rows-[0fr] opacity-0 transition-[grid-template-rows,opacity] duration-300 ease-out" id="faq-answer-4" aria-hidden="true" data-faq-panel>
                        <div class="min-h-0 overflow-hidden">
                            <p class="pt-[7px] text-[10px] leading-[17px] text-[#75818f]">Buka menu Profile, pilih Edit Profil, lalu ubah data yang tersedia dan tekan tombol Simpan Perubahan.</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <aside class="mt-[2px] rounded-[12px] bg-[#e1f1e9] px-[15px] py-[14px]" aria-labelledby="contact-title">
            <div class="flex items-center gap-[9px]">
                <svg class="h-[16px] w-[16px] shrink-0 text-[#205d46]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 11v5m0-8h.01"/>
                </svg>
                <h2 class="text-[11px] font-semibold leading-[16px] text-[#205d46]" id="contact-title">Butuh bantuan terkait pengajuan?</h2>
            </div>
            <p class="mt-[7px] text-[10px] leading-[17px] text-[#32634f]">Bantuan juga tersedia dari Detail Pengajuan. Siapkan ID pengajuan saat menghubungi petugas.</p>
        </aside>

        <footer class="mt-auto pt-[22px] text-center text-[9px] leading-[14px] text-[#7b8794]">E-Sabillilah · Pilah sampah, jaga lingkungan.</footer>
    </main>

    <script>
        document.querySelectorAll('[data-faq-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const panel = document.getElementById(toggle.getAttribute('aria-controls'));
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                const openIcon = toggle.querySelector('[data-icon-open]');
                const closedIcon = toggle.querySelector('[data-icon-closed]');

                toggle.setAttribute('aria-expanded', String(!isOpen));
                panel.setAttribute('aria-hidden', String(isOpen));
                panel.classList.toggle('grid-rows-[1fr]', !isOpen);
                panel.classList.toggle('grid-rows-[0fr]', isOpen);
                panel.classList.toggle('opacity-100', !isOpen);
                panel.classList.toggle('opacity-0', isOpen);
                openIcon.classList.toggle('hidden', isOpen);
                closedIcon.classList.toggle('hidden', !isOpen);
            });
        });

        document.querySelector('[data-back-link]').addEventListener('click', (event) => {
            const referrer = document.referrer;

            if (referrer && new URL(referrer).origin === window.location.origin && window.history.length > 1) {
                event.preventDefault();
                window.history.back();
            }
        });
    </script>
</body>
</html>
