@extends('admin.layouts.warga')

@section('title', 'Data Transaksi')

@section('styles')
    <style>
        .transaction-page { width: 100%; }
        .page-heading-row { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin: 92px 0 17px; }
        .page-heading { margin: 0; color: #252c28; font-size: 19px; font-weight: 700; }
        .page-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .page-action { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; gap: 8px; padding: 0 12px; border: 1px solid #cbd8d2; border-radius: 6px; background: transparent; color: #25352c; font-size: 12px; font-weight: 500; text-decoration: none; white-space: nowrap; }
        .page-action.add { border-color: #07805b; background: #07805b; color: #fff; }
        .page-action svg { width: 14px; height: 14px; flex: 0 0 auto; }
        .search-filter-row { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 9px; margin-bottom: 16px; }
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
        .filter-panel { position: absolute; z-index: 5; top: calc(100% + 7px); right: 0; display: grid; width: min(320px, calc(100vw - 32px)); gap: 10px; padding: 14px; border: 1px solid #dfe8e3; border-radius: 8px; background: #fff; box-shadow: 0 10px 24px rgb(34 79 60 / 12%); }
        .filter-panel label { display: grid; gap: 5px; color: #4a5a51; font-size: 11px; }
        .filter-panel input, .filter-panel select { min-width: 0; min-height: 34px; padding: 5px 8px; border: 1px solid #d5e0da; border-radius: 5px; background: #fff; color: #34443b; font-size: 12px; }
        .filter-dates { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .filter-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .filter-actions button, .filter-actions a { display: inline-flex; min-height: 31px; align-items: center; padding: 0 10px; border: 0; border-radius: 5px; background: #07805b; color: #fff; font-size: 11px; text-decoration: none; cursor: pointer; }
        .filter-actions a { background: #edf3ef; color: #46574d; }
        .table-card { padding: 30px 16px; border-radius: 2px; background: transparent; }
        .table-frame { overflow: hidden; border: 1px solid #dce5e0; border-radius: 8px; background: #fff; }
        .table-scroll { width: 100%; overflow-x: auto; }
        .transaction-table { width: 100%; min-width: 850px; border-collapse: collapse; text-align: left; }
        .transaction-table th { padding: 10px 12px; border-right: 1px solid rgb(255 255 255 / 28%); background: #2F8961; color: #fff; font-size: 12px; font-weight: 550; white-space: nowrap; }
        .transaction-table th:first-child { border-top-left-radius: 7px; }
        .transaction-table th:last-child { border-right: 0; border-top-right-radius: 7px; }
        .transaction-table td { max-width: 240px; padding: 7px 12px; border-right: 1px solid #e6ebe8; border-bottom: 1px solid #e9eeeb; color: #3c4540; font-size: 11px; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .transaction-table tr:nth-child(even) td { background: #f3f4f3; }
        .transaction-table tr:last-child td { border-bottom: 0; }
        .transaction-table td:last-child { border-right: 0; }
        .row-actions { display: flex; align-items: center; gap: 5px; }
        .row-action { display: inline-flex; min-height: 25px; align-items: center; justify-content: center; gap: 4px; padding: 0 7px; border: 0; border-radius: 4px; color: #fff; font-size: 10px; text-decoration: none; cursor: pointer; }
        .row-action.edit, .row-action.pay { background: #07805b; }
        .row-action.delete { background: #e33218; }
        .row-action svg { width: 12px; height: 12px; }
        .delete-form { display: inline; }
        .status-badge { display: inline-flex; min-height: 22px; align-items: center; padding: 0 8px; border-radius: 99px; background: #e9f6ee; color: #286744; font-size: 10px; font-weight: 600; }
        .status-badge.cancelled { background: #fff1ef; color: #a63226; }
        .empty-row { padding: 32px 16px !important; color: #74827a !important; text-align: center; }
        .table-footer { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 30px; }
        .pagination-summary { display: none; }
        .pagination { display: flex; align-items: center; gap: 5px; }
        .page-link { display: grid; min-width: 28px; height: 28px; place-items: center; padding: 0 7px; border: 1px solid #c8d6cf; border-radius: 5px; background: transparent; color: #263b30; font-size: 11px; text-decoration: none; }
        .page-link.active { border-color: #2F8961; background: #2F8961; color: #fff; }
        .page-link.disabled { opacity: .45; pointer-events: none; }
        .notice { margin-bottom: 14px; padding: 11px 14px; border-radius: 7px; background: #e9f6ee; color: #286744; font-size: 12px; }
        .error-notice { background: #fff1ef; color: #a63226; }
        @media (max-width: 900px) { .admin-main { padding: 24px; } .page-heading-row { margin-top: 38px; } .table-card { padding: 22px 12px; } }
        @media (max-width: 600px) { .admin-main { padding: 15px; } .page-heading-row { align-items: flex-start; flex-direction: column; margin: 8px 0 16px; } .page-heading { font-size: 22px; } .page-actions { width: 100%; } .page-action { flex: 1; font-size: 11px; } .search-form { min-width: 100%; } .search-input-wrap { min-width: 0; } .search-submit { display: inline-flex; } .filter-panel { right: 0; } .table-card { padding: 16px 8px; } .table-footer { margin-top: 20px; } .pagination { flex-wrap: wrap; } }
    </style>
@endsection

@section('content')
    <div class="transaction-page">
        <div class="page-heading-row">
            <h1 class="page-heading">Data Transaksi</h1>
            <div class="page-actions">
                <a class="page-action add" href="{{ route('admin.transaksi.create') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add
                </a>
                <a class="page-action" href="{{ route('admin.transaksi.pdf', request()->except('page')) }}">
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
            <form class="search-form" method="GET" action="{{ route('admin.transaksi.index') }}">
                <label class="search-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by invoice number, name, amount...">
                </label>
                @foreach (['status', 'date_from', 'date_to'] as $filterName)
                    @if (($filters[$filterName] ?? '') !== '') <input type="hidden" name="{{ $filterName }}" value="{{ $filters[$filterName] }}"> @endif
                @endforeach
                <button class="search-submit" type="submit">Search</button>
            </form>

            <details class="filter-box" @if (($filters['status'] ?? '') !== '' || ($filters['date_from'] ?? '') !== '' || ($filters['date_to'] ?? '') !== '') open @endif>
                <summary class="filter-toggle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5Z"/></svg>
                    Filter
                </summary>
                <form class="filter-panel" method="GET" action="{{ route('admin.transaksi.index') }}">
                    @if (($filters['search'] ?? '') !== '') <input type="hidden" name="search" value="{{ $filters['search'] }}"> @endif
                    <label for="filter-status">Status pembayaran
                        <select id="filter-status" name="status">
                            <option value="">Semua status</option>
                            <option value="Menunggu" @selected(($filters['status'] ?? '') === 'Menunggu')>Menunggu pembayaran</option>
                            <option value="Selesai" @selected(($filters['status'] ?? '') === 'Selesai')>Lunas</option>
                            <option value="Dibatalkan" @selected(($filters['status'] ?? '') === 'Dibatalkan')>Dibatalkan</option>
                        </select>
                    </label>
                    <div class="filter-dates">
                        <label for="date-from">Dari tanggal
                            <input id="date-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                        </label>
                        <label for="date-to">Sampai tanggal
                            <input id="date-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                        </label>
                    </div>
                    <div class="filter-actions">
                        <a href="{{ route('admin.transaksi.index', array_filter(['search' => $filters['search'] ?? null], fn ($value) => $value !== null && $value !== '')) }}">Reset</a>
                        <button type="submit">Terapkan</button>
                    </div>
                </form>
            </details>
        </div>

        <div class="table-card">
            <div class="table-frame">
                <div class="table-scroll">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>#{{ $transaction->transaction_id }}</td>
                                    <td title="{{ $transaction->full_name }}">{{ $transaction->username }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($transaction->transaction_date)->format('H:i') }}</td>
                                    <td>Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($transaction->transaction_status === 'Menunggu')
                                            <div class="row-actions">
                                                <a class="row-action edit" href="{{ route('admin.transaksi.edit', $transaction->transaction_id) }}" aria-label="Edit transaksi {{ $transaction->transaction_id }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                                                    Edit
                                                </a>
                                                <form class="delete-form" method="POST" action="{{ route('admin.transaksi.destroy', $transaction->transaction_id) }}" onsubmit="return confirm('Hapus transaksi yang masih menunggu ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="row-action delete" type="submit" aria-label="Hapus transaksi {{ $transaction->transaction_id }}">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"/></svg>
                                                        Delete
                                                    </button>
                                                </form>
                                                <a class="row-action pay" href="{{ route('admin.transaksi.payment', $transaction->transaction_id) }}" aria-label="Bayar transaksi {{ $transaction->transaction_id }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                                    Bayar
                                                </a>
                                            </div>
                                        @elseif ($transaction->transaction_status === 'Selesai')
                                            <span class="status-badge">Lunas</span>
                                        @else
                                            <span class="status-badge cancelled">Dibatalkan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="empty-row" colspan="6">Data transaksi tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($transactions->hasPages())
                <div class="table-footer">
                    <span class="pagination-summary">Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi</span>
                    <nav class="pagination" aria-label="Navigasi halaman data transaksi">
                        <a class="page-link {{ $transactions->onFirstPage() ? 'disabled' : '' }}" href="{{ $transactions->previousPageUrl() ?: '#' }}" aria-label="Halaman sebelumnya">&laquo;</a>
                        @foreach ($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                            <a class="page-link {{ $transactions->currentPage() === $page ? 'active' : '' }}" href="{{ $url }}" @if ($transactions->currentPage() === $page) aria-current="page" @endif>{{ $page }}</a>
                        @endforeach
                        <a class="page-link {{ $transactions->hasMorePages() ? '' : 'disabled' }}" href="{{ $transactions->nextPageUrl() ?: '#' }}" aria-label="Halaman berikutnya">&raquo;</a>
                    </nav>
                </div>
            @endif
        </div>
    </div>
@endsection