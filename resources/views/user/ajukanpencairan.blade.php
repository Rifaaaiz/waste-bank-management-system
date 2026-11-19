<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e6f4f0">
    <title>Pencairan Uang - E-Sabilillah</title>
    @vite('resources/css/app.css')
    <style>
        .withdrawal-option input:checked + .withdrawal-card {
            border-color: #2f8961;
        }

        .withdrawal-option input:checked + .withdrawal-card .radio-mark {
            border-color: #2f8961;
            background: #2f8961;
        }

        .withdrawal-option input:checked + .withdrawal-card .radio-dot {
            display: block;
        }

        .withdrawal-option input:focus-visible + .withdrawal-card {
            outline: 2px solid #2f8961;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="min-h-screen bg-[#e6f4f0] font-sans text-[#263a33] antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-[500px] flex-col px-[18px] pb-[20px] pt-[38px]">
        <header>
            <img class="h-[28px] w-[52px] object-contain" src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung">
            <div class="mt-[7px] flex min-h-[22px] items-center gap-[9px]">
                <a
                    class="grid h-[22px] w-[20px] shrink-0 place-items-center"
                    href="{{ route('user.keuangan') }}"
                    aria-label="Kembali ke keuangan"
                >
                    <img class="h-[16px] w-[16px]" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                </a>
                <h1 class="text-[17px] font-bold leading-[22px] text-[#111827]">Pencairan Uang</h1>
            </div>
        </header>

        <form class="mt-[16px] flex flex-1 flex-col" method="POST" action="{{ route('user.keuangan.withdrawals.store') }}">
            @csrf
            <label class="text-[12px] font-semibold leading-[16px] text-[#354353]" for="withdrawal-amount">
                Nominal yang Ingin Dicairkan
            </label>
            <div class="mt-[8px] rounded-[15px] border border-[#2f8961] bg-white px-[16px] py-[14px]">
                <div class="flex items-center gap-[13px]">
                    <span class="text-[17px] font-medium text-[#68777a]">Rp</span>
                    <input
                        class="h-[39px] min-w-0 flex-1 border-0 bg-transparent text-[29px] font-bold leading-[36px] text-[#245f42] outline-none placeholder:text-[#b8c5bf]"
                        id="withdrawal-amount"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9.]*"
                        autocomplete="off"
                        value=""
                        placeholder="0"
                        required
                        aria-describedby="available-balance"
                    >
                    <input
                        id="withdrawal-amount-value"
                        type="hidden"
                        name="amount"
                        value="{{ old('amount') }}"
                    >
                </div>
                <p class="mt-[4px] text-[10px] leading-[14px] text-[#68777a]" id="available-balance">
                    Saldo tersisa: Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </p>
            </div>
            @error('amount')
                <p class="mt-[5px] text-[10px] font-medium text-[#b42318]" role="alert">{{ $message }}</p>
            @enderror

            <fieldset class="mt-[10px]">
                <legend class="text-[12px] font-semibold leading-[16px] text-[#354353]">Metode Pencairan</legend>
                <div class="mt-[8px] grid gap-[9px]">
                    <label class="withdrawal-option block cursor-pointer">
                        <input class="sr-only" type="radio" name="withdrawal_method" value="Datang langsung" data-method-label="Datang langsung" data-method-info="Anda akan mengambil pencairan langsung setelah petugas mengonfirmasi permintaan ini." @checked(old('withdrawal_method', 'Datang langsung') === 'Datang langsung')>
                        <span class="withdrawal-card flex min-h-[66px] items-center gap-[13px] rounded-[15px] border border-[#dce5e1] bg-white px-[14px] py-[10px] transition-colors">
                            <span class="radio-mark grid h-[20px] w-[20px] shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-white">
                                <span class="radio-dot hidden h-[8px] w-[8px] rounded-full bg-white"></span>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[12px] font-bold leading-[16px] text-[#354353]">Datang langsung</span>
                                <span class="mt-[3px] block text-[10px] leading-[14px] text-[#68777a]">Ambil pencairan setelah petugas mengonfirmasi.</span>
                            </span>
                        </span>
                    </label>
                    <label class="withdrawal-option block cursor-pointer">
                        <input class="sr-only" type="radio" name="withdrawal_method" value="ShopeePay" data-method-label="ShopeePay" data-method-info="Pencairan akan diproses melalui transfer ShopeePay." @checked(old('withdrawal_method') === 'ShopeePay')>
                        <span class="withdrawal-card flex min-h-[62px] items-center gap-[13px] rounded-[15px] border border-[#dce5e1] bg-white px-[14px] py-[9px] transition-colors">
                            <span class="radio-mark grid h-[20px] w-[20px] shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-white">
                                <span class="radio-dot hidden h-[8px] w-[8px] rounded-full bg-white"></span>
                            </span>
                            <span class="min-w-0">
                                <img class="h-[18px] max-w-[72px] object-contain object-left" src="{{ asset('images/Spay.png') }}" alt="ShopeePay">
                                <span class="mt-[3px] block text-[10px] leading-[14px] text-[#68777a]">Transfer ShopeePay</span>
                            </span>
                        </span>
                    </label>
                    <label class="withdrawal-option block cursor-pointer">
                        <input class="sr-only" type="radio" name="withdrawal_method" value="DANA" data-method-label="DANA" data-method-info="Pencairan akan diproses melalui transfer DANA." @checked(old('withdrawal_method') === 'DANA')>
                        <span class="withdrawal-card flex min-h-[62px] items-center gap-[13px] rounded-[15px] border border-[#dce5e1] bg-white px-[14px] py-[9px] transition-colors">
                            <span class="radio-mark grid h-[20px] w-[20px] shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-white">
                                <span class="radio-dot hidden h-[8px] w-[8px] rounded-full bg-white"></span>
                            </span>
                            <span class="min-w-0">
                                <img class="h-[18px] max-w-[72px] object-contain object-left" src="{{ asset('images/Dana.png') }}" alt="DANA">
                                <span class="mt-[3px] block text-[10px] leading-[14px] text-[#68777a]">Transfer DANA</span>
                            </span>
                        </span>
                    </label>
                    <label class="withdrawal-option block cursor-pointer">
                        <input class="sr-only" type="radio" name="withdrawal_method" value="BCA Mobile" data-method-label="BCA Mobile" data-method-info="Pencairan akan diproses melalui transfer BCA Mobile." @checked(old('withdrawal_method') === 'BCA Mobile')>
                        <span class="withdrawal-card flex min-h-[62px] items-center gap-[13px] rounded-[15px] border border-[#dce5e1] bg-white px-[14px] py-[9px] transition-colors">
                            <span class="radio-mark grid h-[20px] w-[20px] shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-white">
                                <span class="radio-dot hidden h-[8px] w-[8px] rounded-full bg-white"></span>
                            </span>
                            <span class="min-w-0">
                                <img class="h-[18px] max-w-[72px] object-contain object-left" src="{{ asset('images/Bca.png') }}" alt="BCA Mobile">
                                <span class="mt-[3px] block text-[10px] leading-[14px] text-[#68777a]">Transfer BCA Mobile</span>
                            </span>
                        </span>
                    </label>
                </div>
                @error('withdrawal_method')
                    <p class="mt-[5px] text-[10px] font-medium text-[#b42318]" role="alert">{{ $message }}</p>
                @enderror
            </fieldset>

            <section class="mt-[10px]" aria-labelledby="withdrawal-summary-title">
                <h2 class="text-[12px] font-semibold leading-[16px] text-[#354353]" id="withdrawal-summary-title">Ringkasan pencairan</h2>
                <div class="mt-[7px] rounded-[15px] border border-[#dce5e1] bg-white px-[16px] py-[12px]">
                    <dl class="grid gap-[11px] text-[10px] leading-[14px]">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-[#68777a]">Nominal pencairan</dt>
                            <dd class="font-medium text-[#263a33]" id="summary-amount">Rp 0</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-[#68777a]">Metode</dt>
                            <dd class="text-right font-medium text-[#263a33]" id="summary-method">Datang langsung</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-[#68777a]">Status</dt>
                            <dd class="text-right font-medium text-[#263a33]">Menunggu konfirmasi</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <p class="mt-[8px] rounded-[14px] bg-[#fff3c4] px-[14px] py-[12px] text-[10px] leading-[14px] text-[#8a4b08]" id="withdrawal-info" role="status">
                Anda akan mengambil pencairan langsung setelah petugas mengonfirmasi permintaan ini.
            </p>

            <button
                class="mt-[8px] min-h-[40px] w-full rounded-[9px] bg-[#2f8961] px-4 text-[13px] font-bold text-white transition-colors hover:bg-[#26734f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f8961] disabled:cursor-not-allowed disabled:opacity-50"
                type="submit"
                @disabled($availableBalance < 0.01)
            >
                Konfirmasi Pencairan
            </button>
            @if ($availableBalance < 0.01)
                <p class="mt-[6px] text-center text-[10px] text-[#68777a]">Saldo tersedia belum cukup untuk pencairan.</p>
            @endif
        </form>
    </main>

    <script>
        (() => {
            const amountInput = document.getElementById('withdrawal-amount');
            const amountValue = document.getElementById('withdrawal-amount-value');
            const summaryAmount = document.getElementById('summary-amount');
            const summaryMethod = document.getElementById('summary-method');
            const info = document.getElementById('withdrawal-info');
            const methodInputs = document.querySelectorAll('input[name="withdrawal_method"]');
            const formatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });

            const updateAmount = () => {
                const cursor = amountInput.selectionStart ?? amountInput.value.length;
                const digitsBeforeCursor = amountInput.value.slice(0, cursor).replace(/\D/g, '').length;
                const digits = amountInput.value.replace(/\D/g, '').slice(0, 13);
                const formatted = digits ? formatter.format(Number(digits)) : '';
                let nextCursor = 0;
                let countedDigits = 0;

                while (nextCursor < formatted.length && countedDigits < digitsBeforeCursor) {
                    if (/\d/.test(formatted[nextCursor])) {
                        countedDigits += 1;
                    }
                    nextCursor += 1;
                }

                amountInput.value = formatted;
                amountValue.value = digits;
                summaryAmount.textContent = `Rp ${formatter.format(Number(digits || 0))}`;
                amountInput.setSelectionRange(nextCursor, nextCursor);
            };

            const updateMethod = () => {
                const selectedMethod = document.querySelector('input[name="withdrawal_method"]:checked');

                if (!selectedMethod) {
                    return;
                }

                summaryMethod.textContent = selectedMethod.dataset.methodLabel;
                info.textContent = selectedMethod.dataset.methodInfo;
            };

            amountInput.addEventListener('input', updateAmount);
            methodInputs.forEach((methodInput) => {
                methodInput.addEventListener('change', updateMethod);
            });

            amountInput.value = amountValue.value.replace(/\D/g, '').slice(0, 13);
            updateAmount();
            updateMethod();
        })();
    </script>
</body>
</html>