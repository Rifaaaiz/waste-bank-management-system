<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e6f4f0">
    <title>Notifikasi - E-Sabilillah</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#e6f4f0] font-sans text-[#263a33] antialiased">
    <main class="mx-auto min-h-screen w-full max-w-[500px] px-[18px] pb-10 pt-[38px]">
        <header>
            <img
                class="h-[28px] w-[52px] object-contain"
                src="{{ asset('images/LogoE-Sabililah.png') }}"
                alt="Logo KPP Bina Lindung"
            >
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a
                    class="grid h-[22px] w-[20px] shrink-0 place-items-center"
                    href="{{ $selectedNotification ? route('user.notifications.index') : url()->previous(route('user.dashboard')) }}"
                    aria-label="Kembali"
                >
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Notifikasi</h1>
            </div>
        </header>

        @if (session('status'))
            <p class="mt-3 text-[11px] font-medium text-[#2f8961]" role="status">{{ session('status') }}</p>
        @endif

        @if ($selectedNotification)
            <article class="mt-[20px] border-b border-[#d4dfdb] px-[8px] pb-[17px]">
                <div class="grid grid-cols-[34px_minmax(0,1fr)] gap-[17px]">
                    <img class="mt-[5px] h-[18px] w-[18px] object-contain" src="{{ asset('icons/Cash-icon.png') }}" alt="">
                    <div class="min-w-0">
                        <h2 class="text-[12px] font-bold leading-[16px] text-[#354353]">{{ $selectedNotification['title'] }}</h2>
                        <p class="mt-[7px] text-[10px] leading-[15px] text-[#6b7b7f]">{{ $selectedNotification['message'] }}</p>
                        <p class="mt-[14px] text-[9px] leading-[13px] text-[#6b7b7f]">{{ $selectedNotification['date'] }}</p>
                        <p class="mt-2 text-[10px] text-[#6b7b7f]">Jenis: {{ $selectedNotification['type'] }}</p>
                        @if ($selectedNotification['reference'])
                            <p class="mt-1 text-[10px] text-[#6b7b7f]">Referensi: {{ $selectedNotification['reference'] }}</p>
                        @endif
                        <p class="sr-only">{{ $selectedNotification['is_read'] ? 'Sudah dibaca' : 'Belum dibaca' }}</p>
                    </div>
                </div>
            </article>
        @else
            <div class="mt-[15px]">
                @foreach ($groups as $group)
                    <section class="{{ $group['is_today'] ? '' : 'mt-[12px]' }}" aria-labelledby="notification-group-{{ $loop->index }}">
                        <div class="flex min-h-[22px] items-center justify-between gap-3">
                            <h2 id="notification-group-{{ $loop->index }}" class="text-[16px] font-bold leading-[22px] text-[#374151]">
                                {{ $group['label'] }}
                            </h2>
                            @if ($group['is_today'])
                                <form method="POST" action="{{ route('user.notifications.read-all') }}">
                                    @csrf
                                    <button class="whitespace-nowrap text-[11px] font-semibold text-[#2f8961]" type="submit">
                                        Tandai semua dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        @forelse ($group['items'] as $notification)
                            <article
                                class="grid grid-cols-[34px_minmax(0,1fr)] gap-[17px] border-b border-[#d4dfdb] px-[8px] py-[10px]"
                                aria-label="{{ $notification['is_read'] ? 'Sudah dibaca' : 'Belum dibaca' }}"
                            >
                                <img class="mt-[6px] h-[18px] w-[18px] object-contain" src="{{ asset('icons/Cash-icon.png') }}" alt="">
                                <div class="min-w-0">
                                    <h3 class="text-[12px] {{ $notification['is_read'] ? 'font-semibold' : 'font-bold' }} leading-[16px] text-[#354353]">
                                        {{ $notification['title'] }}
                                    </h3>
                                    <p class="mt-[7px] text-[10px] leading-[15px] text-[#6b7b7f]">{{ $notification['message'] }}</p>
                                    <div class="mt-[16px] flex items-center justify-between gap-3">
                                        <time class="text-[9px] leading-[13px] text-[#6b7b7f]">{{ $notification['date_label'] }}</time>
                                        <a
                                            class="whitespace-nowrap text-[10px] font-semibold text-[#2f8961]"
                                            href="{{ route('user.notifications.show', $notification['id']) }}"
                                        >Lihat detail <span aria-hidden="true">→</span></a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            @if ($group['is_today'])
                                <p class="border-b border-[#d4dfdb] px-[8px] py-[12px] text-[11px] text-[#6b7b7f]">
                                    Belum ada notifikasi hari ini.
                                </p>
                            @endif
                        @endforelse
                    </section>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>