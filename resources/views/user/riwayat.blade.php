<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e6f4f0">
    <title>Riwayat - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e6f4f0] font-sans text-[#263a33] antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[500px] px-[18px] pb-[40px] pt-[38px]">
        <header>
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a
                    class="grid h-[22px] w-[20px] shrink-0 place-items-center"
                    href="{{ route('user.dashboard') }}"
                    aria-label="Kembali"
                >
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Riwayat</h1>
            </div>
        </header>

        <nav class="mt-[13px] grid grid-cols-3 gap-[16px]" role="tablist" aria-label="Jenis riwayat">
            <button
                class="history-type-button rounded-[9px] border border-[#245f42] bg-[#245f42] px-2 py-[9px] text-[12px] font-semibold text-white transition-colors duration-200 ease-out"
                type="button"
                role="tab"
                aria-selected="true"
                data-history-type-filter="all"
            >Semua</button>
            <button
                class="history-type-button rounded-[9px] border border-[#dce5e1] bg-white px-2 py-[9px] text-[12px] font-semibold text-[#344054] transition-colors duration-200 ease-out"
                type="button"
                role="tab"
                aria-selected="false"
                data-history-type-filter="pickup"
            >Pengajuan</button>
            <button
                class="history-type-button rounded-[9px] border border-[#dce5e1] bg-white px-2 py-[9px] text-[12px] font-semibold text-[#344054] transition-colors duration-200 ease-out"
                type="button"
                role="tab"
                aria-selected="false"
                data-history-type-filter="transaction"
            >Transaksi</button>
        </nav>

        <div class="mt-[11px] grid grid-cols-4 gap-[3px]" role="group" aria-label="Filter status">
            <button
                class="history-status-button rounded-full border border-[#dce5e1] bg-[#e8f4ee] px-1 py-[6px] text-[10px] font-medium text-[#245f42] transition-colors duration-200 ease-out"
                type="button"
                aria-pressed="true"
                data-history-status-filter="all"
            >Semua</button>
            <button
                class="history-status-button rounded-full border border-[#dce5e1] bg-white px-1 py-[6px] text-[10px] font-medium text-[#53616a] transition-colors duration-200 ease-out"
                type="button"
                aria-pressed="false"
                data-history-status-filter="menunggu"
            >Menunggu</button>
            <button
                class="history-status-button rounded-full border border-[#dce5e1] bg-white px-1 py-[6px] text-[10px] font-medium text-[#53616a] transition-colors duration-200 ease-out"
                type="button"
                aria-pressed="false"
                data-history-status-filter="selesai"
            >Selesai</button>
            <button
                class="history-status-button rounded-full border border-[#dce5e1] bg-white px-1 py-[6px] text-[10px] font-medium text-[#53616a] transition-colors duration-200 ease-out"
                type="button"
                aria-pressed="false"
                data-history-status-filter="ditolak"
            >Ditolak</button>
        </div>

        <section class="mt-[13px] grid gap-[10px]" id="history-list" role="tabpanel" aria-label="Daftar riwayat">
            @foreach ($entries as $entry)
                <article
                    class="history-entry rounded-[15px] border border-[#dce5e1] bg-white px-[12px] py-[10px]"
                    data-history-entry
                    data-history-kind="{{ $entry['kind'] }}"
                    data-history-status="{{ $entry['filter_status'] }}"
                    aria-label="{{ $entry['kind'] === 'pickup' ? 'Pengajuan' : 'Transaksi' }} dengan status {{ $entry['status'] }}"
                >
                    @if ($entry['kind'] === 'pickup')
                        <div class="flex min-h-[56px] items-center gap-[11px]">
                            <span class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-full bg-[#e8f4ee]">
                                <img class="h-[18px] w-[18px] object-contain" src="{{ asset('icons/Recyle-icon.png') }}" alt="">
                            </span>
                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-[12px] font-bold leading-[16px] text-[#354353]">{{ $entry['title'] }}</h2>
                                <p class="mt-[2px] truncate text-[9px] leading-[13px] text-[#68777a]">{{ $entry['description'] }}</p>
                                <div class="mt-[5px] flex items-center justify-between gap-2">
                                    <time class="truncate text-[9px] leading-[13px] text-[#68777a]">{{ $entry['date'] }}</time>
                                    <span @class([
                                        'min-w-[88px] shrink-0 rounded-full px-[13px] py-[4px] text-center text-[9px] font-semibold leading-[13px]',
                                        'bg-[#dcfce7] text-[#166534]' => $entry['status_tone'] === 'success',
                                        'bg-[#fff2c2] text-[#995900]' => $entry['status_tone'] === 'pending',
                                        'bg-[#fce3e4] text-[#a73739]' => $entry['status_tone'] === 'rejected',
                                        'bg-[#eef0f0] text-[#465250]' => $entry['status_tone'] === 'neutral',
                                    ])>{{ $entry['status'] }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex min-h-[56px] items-start gap-[11px]">
                            <span class="mt-[4px] grid h-[34px] w-[34px] shrink-0 place-items-center rounded-full bg-[#d9fbe6]">
                                <img class="h-[17px] w-[17px] object-contain" src="{{ asset('icons/Centang-icon.png') }}" alt="">
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h2 class="truncate text-[12px] font-bold leading-[16px] text-[#354353]">{{ $entry['title'] }}</h2>
                                    <span class="shrink-0 text-[11px] font-bold leading-[16px] text-[#198754]">{{ $entry['amount'] }}</span>
                                </div>
                                <p class="mt-[2px] truncate text-[9px] leading-[13px] text-[#68777a]">{{ $entry['description'] }}</p>
                                <time class="mt-[5px] block text-[9px] leading-[13px] text-[#68777a]">{{ $entry['date'] }}</time>
                            </div>
                        </div>
                    @endif
                </article>
            @endforeach
        </section>

        <p
            id="history-empty"
            class="{{ $entries->isNotEmpty() ? 'hidden' : '' }} mt-[18px] rounded-[15px] border border-[#dce5e1] bg-white px-4 py-5 text-center text-[12px] text-[#68777a]"
            role="status"
        >Belum ada riwayat pengajuan atau transaksi.</p>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeButtons = Array.from(document.querySelectorAll('[data-history-type-filter]'));
            const statusButtons = Array.from(document.querySelectorAll('[data-history-status-filter]'));
            const entries = Array.from(document.querySelectorAll('[data-history-entry]'));
            const emptyState = document.getElementById('history-empty');
            let activeType = 'all';
            let activeStatus = 'all';

            function updateButtonStates(buttons, activeValue, dataAttribute, selectedClasses, idleClasses, ariaAttribute) {
                buttons.forEach(function (button) {
                    const isActive = button.dataset[dataAttribute] === activeValue;
                    button.classList.remove(...selectedClasses, ...idleClasses);
                    button.classList.add(...(isActive ? selectedClasses : idleClasses));
                    button.setAttribute(ariaAttribute, isActive ? 'true' : 'false');
                });
            }

            function renderEntries() {
                let visibleCount = 0;

                entries.forEach(function (entry) {
                    const matchesType = activeType === 'all' || entry.dataset.historyKind === activeType;
                    const matchesStatus = activeStatus === 'all' || entry.dataset.historyStatus === activeStatus;
                    const isVisible = matchesType && matchesStatus;

                    entry.hidden = !isVisible;
                    visibleCount += isVisible ? 1 : 0;
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
                if (entries.length > 0 && visibleCount === 0) {
                    emptyState.textContent = 'Tidak ada riwayat yang cocok dengan pilihan ini.';
                } else if (entries.length === 0) {
                    emptyState.textContent = 'Belum ada riwayat pengajuan atau transaksi.';
                }
            }

            typeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activeType = this.dataset.historyTypeFilter;
                    updateButtonStates(
                        typeButtons,
                        activeType,
                        'historyTypeFilter',
                        ['bg-[#245f42]', 'border-[#245f42]', 'text-white'],
                        ['bg-white', 'border-[#dce5e1]', 'text-[#344054]'],
                        'aria-selected'
                    );
                    renderEntries();
                });
            });

            statusButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activeStatus = this.dataset.historyStatusFilter;
                    updateButtonStates(
                        statusButtons,
                        activeStatus,
                        'historyStatusFilter',
                        ['bg-[#e8f4ee]', 'border-[#dce5e1]', 'text-[#245f42]'],
                        ['bg-white', 'border-[#dce5e1]', 'text-[#53616a]'],
                        'aria-pressed'
                    );
                    renderEntries();
                });
            });
        });
    </script>
</body>
</html>