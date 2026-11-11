@extends('admin.layouts.warga')

@section('title', 'Edit Petugas')

@section('styles')
    <style>
        .petugas-form-page { width: 100%; }
        .page-heading-row { display: flex; align-items: center; gap: 15px; margin: 20px 0 26px; }
        .back-link { display: grid; width: 42px; height: 42px; flex: 0 0 auto; place-items: center; border-radius: 10px; color: #315343; }
        .back-link svg { width: 21px; height: 21px; }
        .page-heading { margin: 0; color: #26352d; font-size: clamp(24px, 3vw, 32px); font-weight: 700; }
        .form-panel { padding: 30px; border: 1px solid rgb(47 137 97 / 7%); border-radius: 14px; background: #fff; box-shadow: 0 8px 26px rgb(34 79 60 / 6%); }
        @media (max-width: 760px) { .admin-main { padding: 22px; } .page-heading-row { margin-top: 8px; } .form-panel { padding: 24px; } }
        @media (max-width: 600px) { .admin-main { padding: 15px; } .page-heading-row { gap: 11px; margin: 6px 0 20px; } .back-link { width: 38px; height: 38px; } .page-heading { font-size: 24px; } .form-panel { padding: 20px 16px; } }
    </style>
@endsection

@section('content')
    <div class="petugas-form-page">
        <div class="page-heading-row">
            <a class="back-link" href="{{ route('admin.petugas.index') }}" aria-label="Kembali ke Data Petugas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path><path d="M9 12h12"></path></svg>
            </a>
            <h1 class="page-heading">Edit Data Petugas</h1>
        </div>
        <section class="form-panel">
            @include('admin.partials.form-petugas')
        </section>
    </div>
@endsection
