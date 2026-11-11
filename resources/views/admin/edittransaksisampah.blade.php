@extends('admin.layouts.warga')

@section('title', 'Edit Transaksi')

@section('styles')
    <style>
        .transaction-form-page { width: 100%; }
        .page-heading-row { display: flex; align-items: center; gap: 15px; margin: 20px 0 26px; }
        .back-link { display: grid; width: 42px; height: 42px; flex: 0 0 auto; place-items: center; border-radius: 10px; color: #315343; }
        .back-link svg { width: 21px; height: 21px; }
        .page-heading { margin: 0; color: #26352d; font-size: clamp(24px, 3vw, 32px); font-weight: 700; }
        .form-panel { padding: 30px; border: 1px solid rgb(47 137 97 / 7%); border-radius: 14px; background: #fff; box-shadow: 0 8px 26px rgb(34 79 60 / 6%); }
        .form-grid { display: grid; gap: 18px; }
        .form-field { display: grid; min-width: 0; align-content: start; gap: 8px; }
        .form-field label { color: #35463d; font-size: 14px; font-weight: 600; }
        .form-field input, .form-field select, .form-field textarea { width: 100%; min-height: 48px; padding: 10px 14px; border: 1px solid #dce5df; border-radius: 8px; outline: none; background: #fff; color: #26372e; font-size: 14px; }
        .form-field textarea { min-height: 100px; resize: vertical; }
        .form-field input:focus, .form-field select:focus, .form-field textarea:focus { border-color: #2F8961; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
        .field-help { color: #78857d; font-size: 12px; }
        .field-error { color: #bd3527; font-size: 12px; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
        .form-button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; padding: 0 18px; border: 0; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .form-button.primary { background: #2F8961; color: #fff; }
        .form-button.secondary { background: #edf3ef; color: #40564a; }
        @media (max-width: 760px) { .admin-main { padding: 22px; } .page-heading-row { margin-top: 8px; } .form-panel { padding: 24px; } }
        @media (max-width: 600px) { .admin-main { padding: 15px; } .page-heading-row { gap: 11px; margin: 6px 0 20px; } .back-link { width: 38px; height: 38px; } .page-heading { font-size: 24px; } .form-panel { padding: 20px 16px; } }
    </style>
@endsection

@section('content')
    <div class="transaction-form-page">
        <div class="page-heading-row">
            <a class="back-link" href="{{ route('admin.transaksi.index') }}" aria-label="Kembali ke Data Transaksi">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path><path d="M9 12h12"></path></svg>
            </a>
            <h1 class="page-heading">Edit Transaksi #{{ $transaction->transaction_id }}</h1>
        </div>
        <section class="form-panel">
            <form method="POST" action="{{ route('admin.transaksi.update', $transaction->transaction_id) }}">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-field">
                        <label for="pickup">Pickup</label>
                        <select id="pickup" name="pickup" required>
                            @foreach ($pickups as $pickup)
                                <option value="{{ $pickup->pickup_id }}" @selected((string) old('pickup', $transaction->pickup) === (string) $pickup->pickup_id)>
                                    #{{ $pickup->pickup_id }} · {{ $pickup->full_name ?: $pickup->username }} · {{ $pickup->category_name ?: 'Jenis tidak tersedia' }} · {{ number_format((float) $pickup->waste_weight, 2, ',', '.') }} kg
                                </option>
                            @endforeach
                        </select>
                        @error('pickup') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label for="transaction_date">Tanggal dan waktu transaksi</label>
                        <input id="transaction_date" name="transaction_date" type="datetime-local" value="{{ old('transaction_date', \Illuminate\Support\Carbon::parse($transaction->transaction_date)->format('Y-m-d\TH:i')) }}" required>
                        @error('transaction_date') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-field">
                        <label for="transaction_information">Keterangan (opsional)</label>
                        <textarea id="transaction_information" name="transaction_information" maxlength="255">{{ old('transaction_information', $transaction->transaction_information) }}</textarea>
                        @error('transaction_information') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <p class="field-help">Nominal hanya dapat dicatat dari halaman Bayar dan transaksi yang sudah diproses tidak dapat diedit.</p>
                </div>
                <div class="form-actions">
                    <button class="form-button primary" type="submit">Simpan Perubahan</button>
                    <a class="form-button secondary" href="{{ route('admin.transaksi.index') }}">Batal</a>
                </div>
            </form>
        </section>
    </div>
@endsection