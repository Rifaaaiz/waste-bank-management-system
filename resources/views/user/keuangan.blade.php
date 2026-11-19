<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e6f4f0">
    <title>Keuangan - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e6f4f0] font-sans text-[#263a33] antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[500px] px-[18px] pb-[36px] pt-[38px]">
        <header>
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a
                    class="grid h-[22px] w-[20px] shrink-0 place-items-center"
                    href="{{ route('user.dashboard') }}"
                    aria-label="Kembali ke beranda"
                >
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Keuangan</h1>
            </div>
        </header>

        @if (session('status'))
            <p class="mt-3 rounded-[10px] bg-white px-3 py-2 text-[11px] font-medium text-[#2f8961]" role="status">
                {{ session('status') }}
            </p>
        @endif

        <section class="mt-[18px] rounded-[18px] bg-[#2f8961] px-[18px] py-[17px] text-white" aria-label="Saldo Anda">
            <p class="text-[12px] font-medium text-white/85">Saldo Anda</p>
            <div class="mt-[8px] flex items-center justify-between gap-3">
                <p id="balance-value" class="text-[25px] font-bold leading-[32px]" aria-live="polite">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </p>
                <button
                    class="grid h-[38px] w-[38px] shrink-0 place-items-center rounded-full transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    id="balance-toggle"
                    type="button"
                    aria-label="Sembunyikan saldo"
                    aria-pressed="true"
                >
                    <img class="h-[18px] w-[18px] object-contain brightness-0 invert" src="{{ asset('icons/Eye-icon.png') }}" alt="">
                </button>
            </div>
        </section>

        <section class="mt-[12px] grid grid-cols-2 gap-[10px]" aria-label="Ringkasan keuangan">
            <article class="min-h-[91px] rounded-[14px] border border-[#dce5e1] bg-white px-[13px] py-[12px]">
                <p class="text-[10px] font-medium text-[#697873]">Total pemasukan</p>
                <p class="mt-[8px] text-[14px] font-bold text-[#263a33]">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                <span class="mt-[2px] block text-right text-[17px] font-bold leading-[18px] text-[#2f8961]" aria-hidden="true">+</span>
            </article>
            <article class="min-h-[91px] rounded-[14px] border border-[#dce5e1] bg-white px-[13px] py-[12px]">
                <p class="text-[10px] font-medium text-[#697873]">Total pencairan</p>
                <p class="mt-[8px] text-[14px] font-bold text-[#263a33]">Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}</p>
                <span class="mt-[2px] block text-right text-[17px] font-bold leading-[18px] text-[#4c83c3]" aria-hidden="true">−</span>
            </article>
        </section>

        <a
            class="mt-[15px] flex min-h-[43px] items-center justify-center rounded-[12px] bg-[#2f8961] px-4 text-[13px] font-bold text-white transition-colors hover:bg-[#26734f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#245f42]"
            href="{{ route('user.keuangan.withdrawals.create') }}"
        >
            <span aria-hidden="true" class="mr-2 text-[18px] leading-none">+</span>
            Ajukan Pencairan
        </a>

        <section class="mt-[22px]" aria-labelledby="finance-history-title">
            <div class="flex min-h-[30px] items-center justify-between gap-3">
                <h2 class="text-[15px] font-bold text-[#263a33]" id="finance-history-title">Riwayat keuangan</h2>
                <form class="flex shrink-0 items-center gap-[10px]" method="GET" action="{{ route('user.keuangan') }}">
                    <p class="min-w-[91px] text-center text-[10px] font-semibold text-[#596861]">{{ $period->translatedFormat('F Y') }}</p>
                    <label class="relative grid h-[28px] w-[28px] cursor-pointer place-items-center" aria-label="Pilih bulan riwayat">
                        <img class="h-[13px] w-[13px] rotate-180" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                        <input
                            class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                            type="month"
                            name="month"
                            value="{{ $period->format('Y-m') }}"
                            max="{{ now()->format('Y-m') }}"
                            aria-label="Pilih bulan riwayat"
                            data-month-picker
                        >
                    </label>
                </form>
            </div>

            <div class="mt-[9px] grid gap-[9px]">
                @forelse ($entries as $entry)
                    <article class="flex min-h-[69px] items-center gap-[11px] rounded-[13px] border border-[#dce5e1] bg-white px-[12px] py-[10px]">
                        <span @class([
                            'grid h-[35px] w-[35px] shrink-0 place-items-center rounded-full',
                            'bg-[#e8f4ee]' => $entry['kind'] === 'income',
                            'bg-[#eaf2fb]' => $entry['kind'] === 'withdrawal',
                        ])>
                            @if ($entry['kind'] === 'income')
                                <img class="h-[16px] w-[16px] rotate-90 object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                            @else
                                <img class="h-[15px] w-[15px] rotate-[135deg] object-contain" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="truncate text-[11px] font-bold leading-[15px] text-[#354353]">{{ $entry['title'] }}</h3>
                                    <p class="mt-[3px] truncate text-[9px] leading-[13px] text-[#68777a]">{{ $entry['description'] }}</p>
                                </div>
                                <p @class([
                                    'shrink-0 whitespace-nowrap text-[10px] font-bold leading-[15px]',
                                    'text-[#2f8961]' => $entry['kind'] === 'income',
                                    'text-[#4c83c3]' => $entry['kind'] === 'withdrawal' && $entry['status'] === 'Selesai',
                                    'text-[#68777a]' => $entry['kind'] === 'withdrawal' && $entry['status'] !== 'Selesai',
                                ])>{{ $entry['amount'] }}</p>
                            </div>
                            <div class="mt-[5px] flex items-center justify-between gap-2">
                                <time class="truncate text-[9px] leading-[13px] text-[#68777a]">{{ $entry['date'] }}</time>
                                @if ($entry['kind'] === 'withdrawal')
                                    <span @class([
                                        'shrink-0 rounded-full px-[8px] py-[2px] text-[8px] font-semibold leading-[12px]',
                                        'bg-[#dcfce7] text-[#166534]' => $entry['status_tone'] === 'success',
                                        'bg-[#fff2c2] text-[#995900]' => $entry['status_tone'] === 'pending',
                                        'bg-[#fce3e4] text-[#a73739]' => $entry['status_tone'] === 'rejected',
                                        'bg-[#eef0f0] text-[#465250]' => $entry['status_tone'] === 'neutral',
                                    ])>{{ $entry['status'] }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="rounded-[13px] border border-[#dce5e1] bg-white px-4 py-[18px] text-center text-[11px] leading-[16px] text-[#68777a]">
                        Belum ada transaksi keuangan pada bulan ini.
                    </p>
                @endforelse
            </div>
        </section>
    </main>

    <script>
        (() => {
            const balance = document.getElementById('balance-value');
            const toggle = document.getElementById('balance-toggle');
            const monthPicker = document.querySelector('[data-month-picker]');

            if (balance && toggle) {
                const visibleBalance = balance.textContent.trim();
                let isVisible = true;

                toggle.addEventListener('click', () => {
                    isVisible = !isVisible;
                    balance.textContent = isVisible ? visibleBalance : '••••••';
                    toggle.setAttribute('aria-pressed', String(isVisible));
                    toggle.setAttribute('aria-label', isVisible ? 'Sembunyikan saldo' : 'Tampilkan saldo');
                });
            }

            monthPicker?.addEventListener('change', (event) => {
                event.currentTarget.form.submit();
            });
        })();
    </script>
</body>
</html>