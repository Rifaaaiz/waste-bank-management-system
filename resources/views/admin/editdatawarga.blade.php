@extends('admin.layouts.warga')

@section('title', 'Edit Warga')

@section('styles')
    <style>
        .page-heading { margin: 0 0 22px; color: #26352d; font-size: 26px; }
        .form-panel { max-width: 900px; padding: 26px; border-radius: 12px; background: #fff; box-shadow: 0 4px 18px rgb(34 79 60 / 5%); }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .form-field { display: grid; min-width: 0; align-content: start; gap: 7px; }
        .form-field-wide { grid-column: 1 / -1; }
        .form-field label { color: #30453a; font-size: 13px; font-weight: 600; }
        .form-field input,
        .form-field select { width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #d5e0da; border-radius: 7px; outline: none; background: #fff; color: #25352c; }
        .form-field select { appearance: none; padding-right: 36px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23566b5f' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
        .form-field input:focus,
        .form-field select:focus { border-color: #2F8961; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
        .field-error { color: #bd3527; font-size: 12px; }
        .form-actions { display: grid; gap: 8px; margin-top: 2px; }
        .primary-button, .secondary-button { display: inline-flex; width: 100%; min-height: 44px; align-items: center; justify-content: center; padding: 0 17px; border: 0; border-radius: 7px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .primary-button { background: #07805b; color: #fff; }
        .secondary-button { min-height: 36px; background: #edf3ef; color: #40564a; }
        @media (max-width: 620px) { .page-heading { font-size: 22px; } .form-panel { padding: 18px; } .form-grid { grid-template-columns: 1fr; gap: 14px; } .form-field-wide { grid-column: auto; } }
    </style>
@endsection

@section('content')
    <h1 class="page-heading">Edit Data Warga</h1>
    <section class="form-panel">
        @include('admin.partials.form-warga')
    </section>
@endsection