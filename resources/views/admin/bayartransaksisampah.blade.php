@extends('admin.layouts.warga')

@section('title', 'Bayar Transaksi')

@section('styles')
    <style>
        .transaction-form-page { width: 100%; }
        .page-heading-row { display: flex; align-items: center; gap: 15px; margin: 20px 0 26px; }
        .back-link { display: grid; width: 42px; height: 42px; flex: 0 0 auto; place-items: center; border-radius: 10px; color: #315343; }
        .back-link svg { width: 21px; height: 21px; }
        .page-heading { margin: 0; color: #26352d; font-size: clamp(24px, 3vw, 32px); font-weight: 700; }
        .form-panel { max-width: 760px; padding: 30px; border: 1px solid rgb(47 137 97 / 7%); border-radius: 14px; background: #fff; box-shadow: 0 8px 26px rgb(34 79 60 / 6%); }
        .transaction-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 24px; }
        .summary-item { display: grid; gap: 5px; padding: 12px; border-radius: 8px; background: #f2f7f4; }
        .summary-item span { color: #718078; font-size: 12px; }
        .summary-item strong { color: #26372e; font-size: 14px; overflow-wrap: anywhere; }
        .form-field { display: grid; gap: 8px; }
        .form-field label { color: #35463d; font-size: 14px; font-weight: 600; }
        .form-field input { width: 100%; min-height: 48px; padding: 0 14px; border: 1px solid #dce5df; border-radius: 8px; outline: none; background: #fff; color: #26372e; font-size: 14px; }
        .form-field input:focus { border-color: #2F8961; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
        .field-help { color: #78857d; font-size: 12px; line-height: 1.5; }
        .field-error { color: #bd3527; font-size: 12px; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
        .form-button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; padding: 0 18px; border: 0; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .form-button.primary { background: #2F8961; color: #fff; }
        .form-button.secondary { background: #edf3ef; color: #40564a; }
        @media (max-width: 760px) { .admin-main { padding: 22px; } .page-heading-row { margin-top: 8px; } .form-panel { padding: 24px; } }
        @media (max-width: 600px) { .admin-main { padding: 15px; } .page-heading-row { gap: 11px; margin: 6px 0 20px; } .back-link { width: 38px; height: 38px; } .page-heading { font-size: 24px; } .form-panel { padding: 20px 16px; } .transaction-summary { grid-template-columns: 1fr; gap: 9px; } }
    </style>
@endsection

@section('content')
    <div class="transaction-form-page">
        <div class="page-heading-row">
            <a class="back-link" href="{{ route('admin.transaksi.index') }}" aria-label="Kembali ke Data Transaksi">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path><path d="M9 12h12"></path></svg>
            </a>
            <h1 class="page-heading">Bayar Transaksi #{{ $transaction->transaction_id }}</h1>
        </div>
        <section class="form-panel">
            <div class="transaction-summary">
                <div class="summary-item"><span>Pengaju</span><strong>{{ $transaction->full_name ?: $transaction->username }} ({{ $transaction->username }})</strong></div>
                <div class="summary-item"><span>Pickup</span><strong>#{{ $transaction->pickup }}</strong></div>
                <div class="summary-item"><span>Status</span><strong>Menunggu pembayaran</strong></div>
                <div class="summary-item"><span>Nominal saat ini</span><strong>Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</strong></div>
            </div>
            <form method="POST" action="{{ route('admin.transaksi.pay', $transaction->transaction_id) }}">
                @csrf
                <div class="form-field">
                    <label for="amount">Nominal pembayaran</label>
                    <input id="amount" name="amount" type="number" inputmode="decimal" min="0.01" max="9999999999999.99" step="0.01" value="{{ old('amount', $transaction->amount > 0 ? $transaction->amount : '') }}" placeholder="Masukkan nominal hasil penimbangan" required>
                    @error('amount') <span class="field-error">{{ $message }}</span> @enderror
                    <span class="field-help">Setelah disimpan, nominal akan ditambahkan ke saldo akun pengaju. Pembayaran tidak dapat diproses dua kali.</span>
                </div>
                <div class="form-actions">
                    <button class="form-button primary" type="submit">Konfirmasi Pembayaran</button>
                    <a class="form-button secondary" href="{{ route('admin.transaksi.index') }}">Batal</a>
                </div>
            </form>
        </section>
    </div>
@endsection