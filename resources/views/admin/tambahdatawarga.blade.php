@extends('admin.layouts.warga')

@section('title', 'Tambah Warga')

@section('styles')
    <style>
        .warga-form-page { width: 100%; max-width: none; margin: 0; }
        .page-heading-row { display: flex; align-items: center; gap: 15px; margin: 20px 0 26px; }
        .back-link { display: grid; width: 42px; height: 42px; flex: 0 0 auto; place-items: center; border-radius: 10px; color: #315343; }
        .back-link svg { width: 21px; height: 21px; }
        .page-heading { margin: 0; color: #26352d; font-size: clamp(24px, 3vw, 32px); font-weight: 700; letter-spacing: -.025em; }
        .form-panel { padding: 30px; border: 1px solid rgb(47 137 97 / 7%); border-radius: 14px; background: #fff; box-shadow: 0 8px 26px rgb(34 79 60 / 6%); }
        .form-grid { display: grid; grid-template-columns: minmax(0, 1.86fr) minmax(0, 1fr); gap: 20px 24px; }
        .form-field { display: grid; min-width: 0; align-content: start; gap: 8px; }
        .form-field-wide { grid-column: 1 / -1; }
        .form-field label { color: #35463d; font-size: 14px; font-weight: 600; }
        .form-field input,
        .form-field select { width: 100%; min-height: 48px; padding: 0 14px; border: 1px solid #dce5df; border-radius: 8px; outline: none; background: #fff; color: #26372e; font-size: 14px; }
        .form-field input::placeholder { color: #9aa79f; }
        .form-field select { appearance: none; padding-right: 40px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23566b5f' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; }
        .form-field input:focus,
        .form-field select:focus { border-color: #2F8961; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
        .field-error { color: #bd3527; font-size: 12px; }
        .form-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 2px; }
        .primary-button, .secondary-button { display: inline-flex; width: 100%; min-height: 48px; align-items: center; justify-content: center; padding: 0 17px; border: 0; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; transition: background-color 160ms ease, transform 160ms ease; }
        .primary-button { background: #2F8961; color: #fff; }
        .primary-button:hover { background: #256f4e; }
        .primary-button:active { transform: translateY(1px); }
        .secondary-button { min-height: 48px; background: transparent; color: #607167; }
        .secondary-button:hover { background: #f1f6f3; }
        @media (max-width: 760px) {
            .admin-main { padding: 22px; }
            .page-heading-row { margin-top: 8px; }
            .form-panel { padding: 24px; }
        }
        @media (max-width: 600px) {
            .admin-main { padding: 15px; }
            .page-heading-row { gap: 11px; margin: 6px 0 20px; }
            .back-link { width: 38px; height: 38px; }
            .page-heading { font-size: 24px; }
            .form-panel { padding: 20px 16px; }
            .form-grid { grid-template-columns: minmax(0, 1fr); gap: 16px; }
            .form-field-wide { grid-column: auto; }
        }
    </style>
@endsection

@section('content')
    <div class="warga-form-page">
        <div class="page-heading-row">
            <a class="back-link" href="{{ route('admin.warga.index') }}" aria-label="Kembali ke Data Warga">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6"></path>
                    <path d="M9 12h12"></path>
                </svg>
            </a>
            <h1 class="page-heading">Tambah Data Warga</h1>
        </div>
        <section class="form-panel">
            @include('admin.partials.form-warga', ['warga' => null])
        </section>
    </div>
@endsection