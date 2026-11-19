@extends('admin.layouts.warga')

@section('title', 'Data Warga')

@section('styles')
    <style>
        .warga-page { max-width: 1500px; margin: 0 auto; }
        .page-heading-row { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin: 92px 0 17px; }
        .page-heading { margin: 0; color: #252c28; font-size: 19px; font-weight: 700; }
        .page-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .page-action { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; gap: 8px; padding: 0 12px; border: 1px solid #cbd8d2; border-radius: 6px; background: transparent; color: #25352c; font-size: 12px; font-weight: 500; text-decoration: none; white-space: nowrap; }
        .page-action.add { border-color: #07805b; background: #07805b; color: #fff; }
        .page-action svg { width: 14px; height: 14px; flex: 0 0 auto; }
        .search-filter-row { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 9px; margin-bottom: 44px; }
        .search-form { display: flex; min-width: 260px; flex: 1 1 520px; align-items: center; gap: 9px; }
        .search-input-wrap { display: flex; min-height: 36px; flex: 1; align-items: center; gap: 9px; padding: 0 10px; border: 1px solid #e1e9e5; border-radius: 6px; background: #fff; color: #7d8b84; }
        .search-input-wrap svg { width: 14px; height: 14px; flex: 0 0 auto; }
        .search-input-wrap input { width: 100%; min-width: 0; border: 0; outline: 0; background: transparent; color: #34443b; font-size: 11px; }
        .search-input-wrap input::placeholder { color: #87948e; }
        .search-submit, .filter-toggle { display: inline-flex; min-height: 36px; align-items: center; justify-content: center; gap: 7px; padding: 0 11px; border: 1px solid #e1e9e5; border-radius: 6px; background: #fff; color: #52635a; font-size: 12px; cursor: pointer; }
        .search-submit { display: none; }
        .filter-box { position: relative; }
        .filter-toggle { list-style: none; }
        .filter-toggle::-webkit-details-marker { display: none; }
        .filter-toggle svg { width: 14px; height: 14px; }
        .filter-panel { position: absolute; z-index: 5; top: calc(100% + 7px); right: 0; display: grid; width: min(300px, calc(100vw - 32px)); gap: 10px; padding: 14px; border: 1px solid #dfe8e3; border-radius: 8px; background: #fff; box-shadow: 0 10px 24px rgb(34 79 60 / 12%); }
        .filter-panel label { display: grid; gap: 5px; color: #4a5a51; font-size: 11px; }
        .filter-panel select { min-height: 34px; padding: 5px 8px; border: 1px solid #d5e0da; border-radius: 5px; background: #fff; color: #34443b; font-size: 12px; }
        .filter-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .filter-actions button, .filter-actions a { display: inline-flex; min-height: 31px; align-items: center; padding: 0 10px; border: 0; border-radius: 5px; background: #07805b; color: #fff; font-size: 11px; text-decoration: none; cursor: pointer; }
        .filter-actions a { background: #edf3ef; color: #46574d; }
        .table-frame { overflow: hidden; border: 1px solid #dce5e0; border-radius: 8px; background: #fff; }
        .table-scroll { width: 100%; overflow-x: auto; }
        .warga-table { width: 100%; min-width: 850px; border-collapse: collapse; text-align: left; }
        .warga-table th { padding: 10px 12px; border-right: 1px solid rgb(255 255 255 / 28%); background: #2F8961; color: #fff; font-size: 12px; font-weight: 550; white-space: nowrap; }
        .warga-table th:first-child { border-top-left-radius: 7px; }
        .warga-table th:last-child { border-right: 0; border-top-right-radius: 7px; }
        .warga-table td { max-width: 210px; padding: 7px 12px; border-right: 1px solid #e6ebe8; border-bottom: 1px solid #e9eeeb; color: #3c4540; font-size: 11px; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .warga-table tr:nth-child(even) td { background: #f3f4f3; }
        .warga-table tr:last-child td { border-bottom: 0; }
        .warga-table td:last-child { border-right: 0; }
        .row-actions { display: flex; align-items: center; gap: 6px; }
        .row-action { display: inline-flex; width: 55px; height: 24px; min-height: 24px; align-items: center; justify-content: center; gap: 4px; padding: 0; border: 0; border-radius: 4px; color: #fff; font-size: 10px; text-decoration: none; cursor: pointer; }
        .row-action.edit { background: #07805b; }
        .row-action.delete { background: #e33218; }
        .row-action svg { width: 12px; height: 12px; }
        .delete-form { display: inline; }
        .empty-row { padding: 32px 16px !important; color: #74827a !important; text-align: center; }
        .table-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 16px; }
        .pagination-summary { color: #758179; font-size: 11px; }
        .pagination { display: flex; align-items: center; gap: 5px; }
        .page-link { display: grid; min-width: 28px; height: 28px; place-items: center; padding: 0 7px; border: 1px solid #c8d6cf; border-radius: 5px; background: transparent; color: #263b30; font-size: 11px; text-decoration: none; }
        .page-link.active { border-color: #2F8961; background: #2F8961; color: #fff; }
        .page-link.disabled { opacity: .45; pointer-events: none; }
        .notice { margin-bottom: 14px; padding: 11px 14px; border-radius: 7px; background: #e9f6ee; color: #286744; font-size: 12px; }
        .error-notice { background: #fff1ef; color: #a63226; }
        @media (max-width: 900px) { .admin-main { padding: 24px; } .page-heading-row { margin-top: 38px; } .search-filter-row { margin-bottom: 24px; } }
        @media (max-width: 600px) { .admin-main { padding: 15px; } .page-heading-row { align-items: flex-start; flex-direction: column; margin: 8px 0 16px; } .page-heading { font-size: 22px; } .page-actions { width: 100%; } .page-action { flex: 1; font-size: 11px; } .search-form { min-width: 100%; } .search-input-wrap { min-width: 0; } .search-submit { display: inline-flex; } .filter-panel { left: auto; right: 0; } .search-filter-row { margin-bottom: 20px; } .table-footer { align-items: flex-start; flex-direction: column; } .pagination { flex-wrap: wrap; } }
    </style>
@endsection

@section('content')
    <div class="warga-page">
        <div class="page-heading-row">
            <h1 class="page-heading">Data Warga</h1>
            <div class="page-actions">
                <a class="page-action add" href="{{ route('admin.warga.create') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add
                </a>
                <a class="page-action" href="{{ route('admin.warga.pdf', request()->except('page')) }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/></svg>
                    Download PDF Report
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="notice" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->has('delete'))
            <div class="notice error-notice" role="alert">{{ $errors->first('delete') }}</div>
        @endif

        <div class="search-filter-row">
            <form class="search-form" method="GET" action="{{ route('admin.warga.index') }}">
                <label class="search-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by invoice number, name, amount...">
                </label>
                @if (isset($filters['rt']) && $filters['rt'] !== '') <input type="hidden" name="rt" value="{{ $filters['rt'] }}"> @endif
                @if (isset($filters['blok_rumah']) && $filters['blok_rumah'] !== '') <input type="hidden" name="blok_rumah" value="{{ $filters['blok_rumah'] }}"> @endif
                <button class="search-submit" type="submit">Search</button>
            </form>

            <details class="filter-box" @if ((isset($filters['rt']) && $filters['rt'] !== '') || (isset($filters['blok_rumah']) && $filters['blok_rumah'] !== '')) open @endif>
                <summary class="filter-toggle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5Z"/></svg>
                    Filter
                </summary>
                <form class="filter-panel" method="GET" action="{{ route('admin.warga.index') }}">
                    @if (isset($filters['search']) && $filters['search'] !== '') <input type="hidden" name="search" value="{{ $filters['search'] }}"> @endif
                    <label for="filter-rt">RT
                        <select id="filter-rt" name="rt">
                            <option value="">Semua RT</option>
                            @foreach ($rts as $rt)
                                <option value="{{ $rt }}" @selected(($filters['rt'] ?? '') === $rt)>{{ $rt }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label for="filter-blok">Blok Rumah
                        <select id="filter-blok" name="blok_rumah">
                            <option value="">Semua Blok</option>
                            @foreach ($blokRumahs as $blokRumah)
                                <option value="{{ $blokRumah }}" @selected(($filters['blok_rumah'] ?? '') === $blokRumah)>{{ $blokRumah }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="filter-actions">
                        <a href="{{ route('admin.warga.index', array_filter(['search' => $filters['search'] ?? null], fn ($value) => $value !== null && $value !== '')) }}">Reset</a>
                        <button type="submit">Terapkan</button>
                    </div>
                </form>
            </details>
        </div>

        <div class="table-frame">
            <div class="table-scroll">
                <table class="warga-table">
                    <thead>
                        <tr>
                            <th>Nama Warga</th>
                            <th>Username</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat Jalan</th>
                            <th>Blok Rumah</th>
                            <th>RT</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($wargas as $warga)
                            <tr>
                                <td title="{{ $warga->full_name }}">{{ $warga->full_name ?: '—' }}</td>
                                <td>{{ $warga->username }}</td>
                                <td>{{ $warga->phone_number ?: '—' }}</td>
                                <td title="{{ $warga->address }}">{{ $warga->address ?: '—' }}</td>
                                <td>{{ $warga->blok_rumah ?: '—' }}</td>
                                <td>{{ $warga->rt ?: '—' }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a class="row-action edit" href="{{ route('admin.warga.edit', $warga->user_id) }}" aria-label="Edit {{ $warga->username }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                                            Edit
                                        </a>
                                        <form class="delete-form" method="POST" action="{{ route('admin.warga.destroy', $warga->user_id) }}" onsubmit="return confirm('Hapus data warga ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="row-action delete" type="submit" aria-label="Delete {{ $warga->username }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="empty-row" colspan="7">Data warga tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($wargas->hasPages())
            <div class="table-footer">
                <span class="pagination-summary">Menampilkan {{ $wargas->firstItem() }}–{{ $wargas->lastItem() }} dari {{ $wargas->total() }} warga</span>
                <nav class="pagination" aria-label="Navigasi halaman data warga">
                    <a class="page-link {{ $wargas->onFirstPage() ? 'disabled' : '' }}" href="{{ $wargas->previousPageUrl() ?: '#' }}" aria-label="Halaman sebelumnya">&laquo;</a>
                    @foreach ($wargas->getUrlRange(max(1, $wargas->currentPage() - 2), min($wargas->lastPage(), $wargas->currentPage() + 2)) as $page => $url)
                        <a class="page-link {{ $wargas->currentPage() === $page ? 'active' : '' }}" href="{{ $url }}" @if ($wargas->currentPage() === $page) aria-current="page" @endif>{{ $page }}</a>
                    @endforeach
                    <a class="page-link {{ $wargas->hasMorePages() ? '' : 'disabled' }}" href="{{ $wargas->nextPageUrl() ?: '#' }}" aria-label="Halaman berikutnya">&raquo;</a>
                </nav>
            </div>
        @endif
    </div>
@endsection