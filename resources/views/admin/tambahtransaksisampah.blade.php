@extends('admin.layouts.warga')

@section('title', 'Tambah Transaksi Sampah')

@section('styles')
    <style>
        .transaction-create-page { width: 100%; }
        .page-heading-row { display: flex; align-items: center; gap: 12px; margin: 54px 0 20px; }
        .back-link { display: grid; width: 24px; height: 32px; flex: 0 0 auto; place-items: center; color: #17231d; text-decoration: none; }
        .back-link img { width: 16px; height: 16px; filter: brightness(0) saturate(100%); }
        .page-heading { margin: 0; color: #252c28; font-size: clamp(24px, 3vw, 32px); font-weight: 700; line-height: 1.2; }
        .transaction-form { display: grid; gap: 12px; }
        .form-field { display: grid; min-width: 0; align-content: start; gap: 5px; }
        .form-field label { color: #303833; font-size: 15px; font-weight: 650; }
        .form-field input, .form-field select { width: 100%; min-height: 36px; padding: 0 10px; border: 0; border-radius: 7px; outline: 0; background: #fff; color: #39433d; font-size: 13px; }
        .form-field input::placeholder, .form-field select:invalid { color: #929a95; }
        .form-field input:focus, .form-field select:focus { box-shadow: 0 0 0 3px rgb(47 137 97 / 18%); }
        .form-field input[readonly] { color: #68756e; }
        .form-field select option { color: #39433d; }
        .input-with-icon { position: relative; }
        .input-with-icon input { padding-right: 38px; }
        .input-icon { position: absolute; top: 50%; right: 12px; display: grid; width: 16px; height: 16px; place-items: center; color: #101713; pointer-events: none; transform: translateY(-50%); }
        .input-icon img { display: block; width: 16px; height: 16px; object-fit: contain; filter: brightness(0) saturate(100%); }
        .input-icon.arrow-right img { transform: rotate(180deg); }
        .date-time-row, .form-actions { display: grid; grid-template-columns: minmax(0, 65fr) minmax(0, 35fr); gap: 14px; }
        .field-error { color: #a63226; font-size: 12px; }
        .empty-note { padding: 16px; border: 1px solid rgb(47 137 97 / 9%); border-radius: 8px; background: rgb(255 255 255 / 65%); color: #4a5a51; font-size: 13px; line-height: 1.5; }
        .form-actions { margin-top: -2px; }
        .submit-button { grid-column: 2; width: 100%; min-height: 36px; border: 0; border-radius: 7px; background: #07805b; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: background-color 160ms ease, transform 160ms ease; }
        .submit-button:hover { background: #066f50; }
        .submit-button:active { transform: translateY(1px); }
        .submit-button:disabled { cursor: not-allowed; }
        @media (max-width: 900px) { .admin-main { padding: 24px; } .page-heading-row { margin-top: 40px; } }
        @media (max-width: 760px) { .page-heading-row { margin-top: 8px; } }
        @media (max-width: 600px) {
            .admin-main { padding: 15px; }
            .page-heading-row { gap: 9px; margin: 8px 0 22px; }
            .page-heading { font-size: 24px; }
            .transaction-form { gap: 14px; }
            .date-time-row, .form-actions { grid-template-columns: 1fr; gap: 14px; }
            .submit-button { grid-column: 1; min-height: 44px; }
        }
    </style>
@endsection

@section('content')
    <div class="transaction-create-page">
        <div class="page-heading-row">
            <a class="back-link" href="{{ route('admin.transaksi.index') }}" aria-label="Kembali ke Data Transaksi">
                <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
            </a>
            <h1 class="page-heading">Tambah Transaksi Sampah</h1>
        </div>

        <form class="transaction-form" method="POST" action="{{ route('admin.transaksi.store') }}" id="transaction-create-form">
                @csrf
                <div class="form-field">
                    <label for="pickup">Username</label>
                    <select id="pickup" name="pickup" required @disabled($pickups->isEmpty())>
                        <option value="" disabled @selected(old('pickup') === null)>{{ $pickups->isEmpty() ? 'Belum ada pickup selesai' : 'Masukan Username' }}</option>
                        @foreach ($pickups as $pickup)
                            @php
                                $pickupDate = $pickup->pickup_finished ?? $pickup->updated_at ?? $pickup->created_at;
                                $pickupDateTime = $pickupDate ? \Illuminate\Support\Carbon::parse($pickupDate) : null;
                            @endphp
                            <option
                                value="{{ $pickup->pickup_id }}"
                                data-date="{{ $pickupDateTime?->format('d-m-Y') ?? 'Tanggal tidak tersedia' }}"
                                data-time="{{ $pickupDateTime?->format('H:i') ?? 'Waktu tidak tersedia' }}"
                                data-raw-date="{{ $pickupDateTime?->format('Y-m-d') ?? '' }}"
                                data-category="{{ $pickup->category_name ?: 'Jenis sampah tidak tersedia' }}"
                                data-weight="{{ number_format((float) $pickup->waste_weight, 2, ',', '.') }} kg"
                                data-amount="{{ number_format((float) $pickup->calculated_amount, 0, ',', '.') }}"
                                @selected((string) old('pickup') === (string) $pickup->pickup_id)
                            >
                                {{ $pickup->username }} · {{ $pickup->full_name ?: $pickup->username }} · Pickup #{{ $pickup->pickup_id }} · {{ $pickup->category_name ?: 'Jenis sampah tidak tersedia' }} · {{ number_format((float) $pickup->waste_weight, 2, ',', '.') }} kg
                            </option>
                        @endforeach
                    </select>
                    @error('pickup') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="date-time-row">
                    <div class="form-field">
                        <label for="transaction-date">Tanggal</label>
                        <div class="input-with-icon">
                            <input id="transaction-date" type="date" data-preview="date" aria-label="Tanggal pickup selesai" readonly>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="transaction-time">Waktu</label>
                        <div class="input-with-icon">
                            <input id="transaction-time" type="text" data-preview="time" placeholder="Pilih Waktu" readonly>
                            <span class="input-icon arrow-right" aria-hidden="true">
                                <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label for="transaction-amount">Nominal</label>
                    <input id="transaction-amount" type="text" data-preview="amount" placeholder="Masukan Nominal" readonly>
                </div>

                <div class="form-actions">
                    <button class="submit-button" type="submit" @disabled($pickups->isEmpty())>Submit</button>
                </div>
                @if ($pickups->isEmpty())
                    <p class="empty-note">Form tetap tersedia. Admin dapat mencatat transaksi setelah ada pickup Warga yang selesai dan belum memiliki transaksi.</p>
                @endif
        </form>
    </div>
    <script>
        (() => {
            const pickupSelect = document.getElementById('pickup');
            const dateInput = document.getElementById('transaction-date');
            const timeInput = document.getElementById('transaction-time');
            const amountInput = document.getElementById('transaction-amount');

            if (!pickupSelect || !dateInput || !timeInput || !amountInput) {
                return;
            }

            const updatePickupPreview = () => {
                const selectedPickup = pickupSelect.selectedOptions[0];
                const hasSelection = Boolean(selectedPickup?.value);

                if (!hasSelection) {
                    dateInput.value = '';
                    timeInput.value = '';
                    amountInput.value = '';
                    return;
                }

                dateInput.value = selectedPickup.dataset.rawDate;
                timeInput.value = selectedPickup.dataset.time;
                amountInput.value = `Rp ${selectedPickup.dataset.amount}`;
            };

            pickupSelect.addEventListener('change', updatePickupPreview);
            updatePickupPreview();
        })();
    </script>
@endsection
