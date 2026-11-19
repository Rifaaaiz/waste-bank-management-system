<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#e6f4f0">
    <title>Beranda Warga - E-Sabilillah</title>
    @vite('resources/css/app.css')
    <style>
        :root {
            --bg-soft: #e6f4f0;
            --bg-panel: #f6fbf4;
            --green-900: #123d2e;
            --green-800: #245f42;
            --green-700: #2f8961;
            --green-600: #2f8961;
            --green-500: #61c27f;
            --green-400: #91c923;
            --green-200: #d8f0d3;
            --green-100: #eefaf2;
            --text-main: #1a2d24;
            --text-soft: #68777a;
            --line-soft: #e1e9e6;
            --white: #fff;
            --badge-wait: #f8f0b8;
            --badge-text: #7a5a00;
            --shadow: 0 10px 20px rgba(25, 74, 52, 0.06);
            --radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            background: var(--bg-soft);
            color: var(--text-main);
            font-family: Inter, "Segoe UI", sans-serif;
        }

        body {
            min-height: 100vh;
        }

        img {
            display: block;
            max-width: 100%;
        }

        button, a {
            font: inherit;
        }

        .user-page {
            min-height: 100vh;
            background: #e6f4f0;
        }

        .user-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 42px 18px 78px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 36px;
            margin-bottom: 6px;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-mark img {
            width: 58px;
            height: 38px;
            object-fit: contain;
        }

        .logout-form {
            margin: 0;
        }

        .logout-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 9px;
            border: 1px solid #879a93;
            border-radius: 999px;
            background: transparent;
            color: var(--green-900);
            font-size: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.16s ease, background 0.16s ease;
        }

        .logout-button:hover,
        .logout-button:focus-visible {
            transform: translateY(-1px);
            background: rgba(47, 137, 97, 0.12);
        }

        .logout-button img {
            width: 12px;
            height: 12px;
        }

        .user-greeting {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .user-name {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--green-900);
            text-decoration: none;
        }

        .user-name img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(47, 137, 97, 0.12);
            padding: 4px;
        }

        .user-name span {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icon-button {
            display: grid;
            place-items: center;
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 50%;
            background: transparent;
            cursor: pointer;
            transition: transform 0.16s ease, background 0.16s ease;
        }

        .icon-button:hover,
        .icon-button:focus-visible {
            transform: translateY(-1px);
            background: rgba(47, 137, 97, 0.12);
        }

        .icon-button img {
            width: 22px;
            height: 22px;
        }

        .segmented-nav {
            position: relative;
            isolation: isolate;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0;
            padding: 4px;
            margin-bottom: 16px;
            border-radius: 999px;
            background: #f3f5f4;
        }

        .segmented-nav::before {
            position: absolute;
            z-index: 0;
            top: 4px;
            left: 4px;
            width: calc((100% - 8px) / 2);
            height: calc(100% - 8px);
            border-radius: 999px;
            background: var(--green-700);
            content: "";
            pointer-events: none;
        }

        .segmented-nav.ready::before {
            transition: transform 450ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .segmented-nav.statistics-active::before {
            transform: translateX(100%);
        }

        .tab-button {
            position: relative;
            z-index: 1;
            border: none;
            border-radius: 999px;
            padding: 8px 12px;
            background: transparent;
            color: var(--green-800);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: color 450ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .tab-button.is-active {
            background: transparent;
            color: var(--white);
        }

        .dashboard-panel {
            display: none;
        }

        .dashboard-panel.is-active {
            display: block;
        }

        .dashboard-panel.panel-switch-forward {
            animation: panel-switch-forward 0.3s cubic-bezier(0.2, 0.75, 0.25, 1) both;
        }

        .dashboard-panel.panel-switch-back {
            animation: panel-switch-back 0.3s cubic-bezier(0.2, 0.75, 0.25, 1) both;
        }

        @keyframes panel-switch-forward {
            from {
                opacity: 0;
                transform: translateX(14px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes panel-switch-back {
            from {
                opacity: 0;
                transform: translateX(-14px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .card {
            border-radius: var(--radius);
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .balance-card {
            padding: 18px 18px;
            border-radius: 14px;
            background: var(--green-700);
            color: var(--white);
            box-shadow: none;
        }

        .balance-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 17px;
        }

        .balance-label {
            font-size: 15px;
            font-weight: 700;
        }

        .online-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 650;
            color: rgba(255, 255, 255, 0.9);
        }

        .online-status .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #a9f39f;
            box-shadow: 0 0 0 4px rgba(169, 243, 159, 0.15);
        }

        .balance-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 0;
        }

        .amount-wrap {
            display: flex;
            align-items: baseline;
            gap: 5px;
            min-width: 0;
            font-weight: 800;
        }

        .currency-label {
            font-size: 19px;
            letter-spacing: 0.08em;
            opacity: 0.9;
        }

        .amount-value {
            font-size: 19px;
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .balance-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hide-balance {
            display: grid;
            place-items: center;
            width: 24px;
            height: 24px;
            border: 0;
            border-radius: 50%;
            background: transparent;
            cursor: pointer;
            transition: background 0.16s ease, transform 0.16s ease;
        }

        .hide-balance:hover,
        .hide-balance:focus-visible {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.12);
        }

        .hide-balance svg {
            width: 18px;
            height: 18px;
        }

        .withdraw-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 9px;
            padding: 11px 15px;
            background: #176044;
            color: var(--white);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .withdraw-button:disabled {
            opacity: 0.9;
            cursor: not-allowed;
        }

        .feature-panel {
            margin-top: 14px;
        }

        .section-title {
            margin: 0 0 7px;
            font-size: 14px;
            font-weight: 800;
            color: var(--green-900);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .feature-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 92px;
            border: 1px solid rgba(47, 137, 97, 0.08);
            border-radius: 13px;
            background: var(--white);
            color: var(--text-main);
            text-align: center;
            text-decoration: none;
            box-shadow: 0 12px 22px rgba(20, 56, 42, 0.04);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .feature-card:hover,
        .feature-card:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 18px 24px rgba(20, 56, 42, 0.08);
        }

        .feature-card:disabled {
            opacity: 1;
            cursor: not-allowed;
        }

        .feature-icon-wrap {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(145, 201, 35, 0.12);
        }

        .feature-icon-wrap img {
            width: 18px;
            height: 18px;
        }

        .feature-card span {
            font-size: 12px;
            font-weight: 700;
            color: var(--green-900);
        }

        .activity-section {
            margin-top: 24px;
            padding-bottom: 10px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .section-head a {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--green-700);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .view-all-arrow {
            display: inline-block;
            flex: none;
            width: 12px;
            height: 12px;
            background-color: currentColor;
            -webkit-mask: url("{{ asset('icons/arrow-icon.png') }}") center / contain no-repeat;
            mask: url("{{ asset('icons/arrow-icon.png') }}") center / contain no-repeat;
            transform: rotate(180deg);
        }

        .activity-list {
            display: grid;
            gap: 10px;
        }

        .activity-card {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            min-height: 78px;
            padding: 10px 12px;
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: var(--white);
            box-shadow: 0 10px 20px rgba(20, 56, 42, 0.04);
        }

        .activity-icon {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(47, 137, 97, 0.08);
        }

        .activity-icon img {
            width: 18px;
            height: 18px;
        }

        .activity-icon-success {
            background: #d7f8e3;
        }

        .activity-copy {
            min-width: 0;
        }

        .activity-title {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            color: var(--green-900);
        }

        .activity-meta {
            margin-top: 2px;
            font-size: 10px;
            color: var(--text-soft);
        }

        .activity-date {
            margin-top: 2px;
            font-size: 9px;
            color: var(--text-soft);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 88px;
            padding: 6px 8px;
            border-radius: 999px;
            background: var(--badge-wait);
            color: var(--badge-text);
            font-size: 9px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .status-success {
            background: #d9f3e3;
            color: #176044;
        }

        .status-cancelled {
            background: #fde4df;
            color: #9c3d2c;
        }

        .empty-state {
            padding: 16px 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.8);
            color: var(--text-soft);
            font-size: 13px;
            text-align: center;
        }

        .stats-card {
            padding: 14px 10px 16px;
            background: var(--white);
            border-radius: 13px;
            box-shadow: none;
        }

        .stats-card .section-title {
            margin-bottom: 0;
        }

        .period-nav {
            display: grid;
            grid-template-columns: 32px minmax(0, 1fr) 32px;
            align-items: center;
            gap: 8px;
            margin: -7px 0 11px;
            text-align: center;
        }

        .period-nav a,
        .period-nav span {
            color: var(--text-main);
            text-decoration: none;
        }

        .period-arrow {
            display: grid;
            place-items: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .period-arrow img {
            width: 18px;
            height: 18px;
        }

        .period-arrow.previous img {
            transform: none;
        }

        .period-arrow.next img {
            transform: rotate(180deg);
        }

        .period-arrow.disabled {
            visibility: hidden;
        }

        .period-copy span,
        .period-copy strong {
            display: block;
        }

        .period-copy span {
            font-size: 12px;
        }

        .period-copy strong {
            margin-top: 2px;
            font-size: 12px;
            font-weight: 500;
        }

        .chart-card {
            padding: 12px 10px 16px;
            border-radius: 13px;
            background: #fff;
        }

        .chart-title {
            margin: 0;
            color: var(--green-900);
            font-size: 14px;
            font-weight: 800;
        }

        .chart-area {
            display: grid;
            min-height: 158px;
            place-items: center;
        }

        .donut-chart {
            position: relative;
            display: grid;
            width: 124px;
            height: 124px;
            place-items: center;
            border-radius: 50%;
            background: var(--chart);
            transform: rotate(-90deg);
        }

        .donut-chart::after {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #fff;
            content: "";
        }

        .donut-total {
            position: relative;
            z-index: 1;
            display: grid;
            place-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            color: #141414;
            text-align: center;
            transform: rotate(90deg);
        }

        .donut-total span {
            font-size: 9px;
            line-height: 1.35;
        }

        .donut-total strong {
            font-size: 10px;
            font-weight: 600;
        }

        .chart-legend-head,
        .chart-legend-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 72px;
            align-items: center;
            gap: 8px;
            padding: 0 18px;
        }

        .chart-legend-head {
            margin-bottom: 8px;
            color: var(--green-900);
            font-size: 12px;
            font-weight: 750;
        }

        .chart-legend-head span:last-child,
        .chart-legend-row strong {
            text-align: right;
        }

        .chart-legend-row {
            min-height: 25px;
            color: #929292;
            font-size: 12px;
            font-weight: 600;
        }

        .legend-category {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 8px;
        }

        .legend-dot {
            flex: 0 0 14px;
            width: 14px;
            height: 14px;
            border-radius: 4px;
        }

        .chart-legend-row strong {
            font-weight: 600;
        }

        .rank-section {
            margin-top: 28px;
        }

        .rank-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-auto-rows: 62px;
            gap: 8px;
            padding: 0 7px;
        }

        .rank-card {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 8px;
            padding: 10px;
            border-radius: 9px;
            background: #fff;
            color: var(--text-soft);
            font-size: 12px;
            font-weight: 700;
        }

        .rank-card-primary {
            grid-row: span 2;
            flex-direction: column;
            align-items: stretch;
            padding: 12px;
        }

        .rank-card-side {
            align-items: flex-start;
        }

        .rank-card-wide {
            grid-column: 1 / -1;
            min-height: 62px;
            margin-top: 4px;
        }

        .rank-category {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 6px;
        }

        .rank-color {
            display: grid;
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 9px;
        }

        .rank-color img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .rank-card-primary .rank-color {
            flex-basis: 34px;
        }

        .rank-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rank-count {
            display: block;
            margin-top: 3px;
            font-size: 11px;
            font-weight: 600;
        }

        .rank-card-primary .rank-metric {
            margin-top: auto;
            color: #8c8c8c;
            text-align: center;
        }

        .rank-metric strong {
            display: block;
            font-size: 30px;
            line-height: 1.1;
        }

        .rank-metric span {
            display: block;
            margin-top: 7px;
            font-size: 16px;
        }

        .rank-card-wide .rank-count {
            margin: 0 0 0 auto;
            white-space: nowrap;
        }

        .stats-empty {
            padding: 20px 10px;
            color: var(--text-soft);
            font-size: 12px;
            text-align: center;
        }

        .wa-button {
            position: fixed;
            right: 14px;
            bottom: max(52px, env(safe-area-inset-bottom));
            z-index: 20;
            display: grid;
            place-items: center;
            width: 52px;
            height: 52px;
            border: none;
            border-radius: 50%;
            background: var(--white);
            box-shadow: 0 18px 30px rgba(26, 45, 36, 0.12);
            color: #00c96b;
            cursor: not-allowed;
            opacity: 0.9;
        }

        .wa-button span {
            color: #00c96b;
            font-size: 15px;
            font-weight: 800;
        }

        @media (min-width: 640px) {
            .user-container { max-width: 500px; }
        }

        @media (max-width: 360px) {
            .activity-card {
                grid-template-columns: 30px minmax(0, 1fr) auto;
                gap: 8px;
                padding-inline: 9px;
            }

            .status-badge {
                min-width: 76px;
            }

            .amount-value,
            .currency-label {
                font-size: 17px;
            }

            .withdraw-button {
                padding-inline: 11px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }

            .segmented-nav.ready::before,
            .segmented-nav.ready .tab-button {
                transition-duration: 450ms !important;
            }
        }

    </style>
</head>
<body>
    <div class="user-page min-h-screen bg-[#e6f4f0]">
        <div class="user-container mx-auto w-full max-w-125 px-18 pb-19.5">
            <header class="topbar">
                <div class="brand-mark" aria-label="Logo KPP Bina Lindung">
                    <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung">
                </div>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-button" aria-label="Keluar">
                        <img src="{{ asset('icons/logout-icon.png') }}" alt="">
                        <span>Keluar</span>
                    </button>
                </form>
            </header>

            <div class="user-greeting">
                <a class="user-name" href="{{ route('user.profile.show') }}" aria-label="Profile {{ $user->full_name ?: $user->username }}">
                    <img src="{{ asset('icons/Profile-icon.png') }}" alt="">
                    <span>Hai, {{ $user->full_name ?: $user->username }}</span>
                </a>

                <div class="header-icons">
                    <a class="icon-button" href="{{ route('user.notifications.index') }}" aria-label="Notifikasi">
                        <img src="{{ asset('icons/Notification-icon.png') }}" alt="">
                    </a>
                </div>
            </div>

            <nav class="segmented-nav" id="dashboard-tabs" role="tablist" aria-label="Navigasi utama">
                <button type="button" class="tab-button is-active" id="home-tab" role="tab" aria-selected="true" aria-controls="home-panel" data-target="home-panel">Home</button>
                <button type="button" class="tab-button" id="statistik-tab" role="tab" aria-selected="false" aria-controls="statistik-panel" data-target="statistik-panel">Statistik</button>
            </nav>

            <main>
                <section id="home-panel" class="dashboard-panel is-active" role="tabpanel" aria-labelledby="home-tab">
                    <div class="balance-card card" id="balance-card">
                        <div class="balance-header">
                            <div class="balance-label">Saldo Anda</div>
                            <div class="online-status"><span class="dot"></span> Online</div>
                        </div>

                        <div class="balance-row">
                            <div class="amount-wrap">
                                <span class="currency-label">RP.</span>
                                <span class="amount-value" id="balance-value">{{ number_format($balance, 0, ',', '.') }}</span>
                                <button type="button" id="toggle-balance" class="hide-balance" aria-label="Sembunyikan saldo">
                                    <img src="{{ asset('icons/Eye-icon.png') }}" alt="">
                                </button>
                            </div>

                            <div class="balance-actions">
                                <a class="withdraw-button" href="{{ route('user.keuangan.withdrawals.create') }}">Tarik Saldo</a>
                            </div>
                        </div>
                    </div>

                    <div class="feature-panel">
                        <h2 class="section-title">Fitur Pilihan</h2>
                        <div class="feature-grid">
                            <button type="button" class="feature-card" aria-label="Jual Sampah" disabled title="Pengajuan sampah belum tersedia">
                                <span class="feature-icon-wrap">
                                    <img src="{{ asset('icons/Recyle-icon.png') }}" alt="Jual Sampah">
                                </span>
                                <span>Jual Sampah</span>
                            </button>

                            <a class="feature-card" href="{{ route('user.riwayat') }}" aria-label="Riwayat aktivitas">
                                <span class="feature-icon-wrap">
                                    <img src="{{ asset('icons/History-icon.png') }}" alt="Riwayat">
                                </span>
                                <span>Riwayat</span>
                            </a>

                            <a class="feature-card" href="{{ route('user.keuangan') }}" aria-label="Saldo keuangan">
                                <span class="feature-icon-wrap">
                                    <img src="{{ asset('icons/Cash-icon.png') }}" alt="Keuangan">
                                </span>
                                <span>Keuangan</span>
                            </a>
                        </div>
                    </div>

                    <section class="activity-section" id="activity-section">
                        <div class="section-head">
                            <h2 class="section-title">Aktivitas terbaru</h2>
                            <a class="view-all-link" href="{{ route('user.riwayat') }}">
                                Lihat semua
                                <span class="view-all-arrow" aria-hidden="true"></span>
                            </a>
                        </div>

                        @if (! empty($activities))
                            <div class="activity-list" id="activity-list">
                                @foreach ($activities as $activity)
                                    <div class="activity-card">
                                        <div class="activity-icon{{ $activity['kind'] === 'transaction' && $activity['status'] === 'Selesai' ? ' activity-icon-success' : '' }}">
                                            <img src="{{ asset($activity['kind'] === 'transaction' ? ($activity['status'] === 'Selesai' ? 'icons/Centang-icon.png' : 'icons/Cash-icon.png') : 'icons/Recyle-icon.png') }}" alt="">
                                        </div>
                                        <div class="activity-copy">
                                            <p class="activity-title">{{ $activity['title'] }}</p>
                                            <div class="activity-meta">{{ $activity['description'] }}</div>
                                            <div class="activity-date">{{ $activity['date'] }}</div>
                                        </div>
                                        <div class="status-badge{{ $activity['status'] === 'Selesai' ? ' status-success' : ($activity['status'] === 'Dibatalkan' ? ' status-cancelled' : '') }}">{{ $activity['status'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">Belum ada aktivitas terbaru untuk akun ini.</div>
                        @endif
                    </section>
                </section>

                <section id="statistik-panel" class="dashboard-panel" role="tabpanel" aria-labelledby="statistik-tab" hidden>
                    <nav class="period-nav" aria-label="Pilih periode statistik">
                        <a class="period-arrow previous" href="{{ route('user.dashboard', ['month' => $statistics['previous_month'], 'tab' => 'statistics']) }}" aria-label="Periode sebelumnya">
                            <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
                        </a>
                        <div class="period-copy">
                            <span>Periode</span>
                            <strong>{{ $statistics['period_start'] }} - {{ $statistics['period_end'] }}</strong>
                        </div>
                        @if ($statistics['has_next_month'])
                            <a class="period-arrow next" href="{{ route('user.dashboard', ['month' => $statistics['next_month'], 'tab' => 'statistics']) }}" aria-label="Periode berikutnya">
                                <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
                            </a>
                        @else
                            <span class="period-arrow next disabled" aria-hidden="true">
                                <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
                            </span>
                        @endif
                    </nav>

                    <div class="stats-card">
                        <section class="chart-card" aria-labelledby="waste-summary-title">
                            <h2 id="waste-summary-title" class="chart-title">Ringkasan Sampah</h2>

                            @if ($statistics['categories'] !== [])
                                <div class="chart-area">
                                    <div class="donut-chart" style="--chart: {{ $statistics['chart'] }};" role="img" aria-label="Diagram komposisi sampah periode ini">
                                        <div class="donut-total">
                                            <span>Total</span>
                                            <strong>{{ number_format($statistics['total_weight'], fmod($statistics['total_weight'], 1) === 0.0 ? 0 : 1, ',', '.') }}</strong>
                                            <span>Item</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="chart-legend-head">
                                    <span>Jenis Sampah</span>
                                    <span>{{ $period->translatedFormat('M') }}</span>
                                </div>

                                @foreach ($statistics['categories'] as $category)
                                    <div class="chart-legend-row">
                                        <span class="legend-category">
                                            <span class="legend-dot" style="background: {{ $category['color'] }};"></span>
                                            <span>{{ $category['name'] }}</span>
                                        </span>
                                        <strong>{{ number_format($category['count'], fmod($category['count'], 1) === 0.0 ? 0 : 1, ',', '.') }}</strong>
                                    </div>
                                @endforeach
                            @else
                                <div class="stats-empty">Belum ada data setoran pada periode ini.</div>
                            @endif
                        </section>

                        <section class="rank-section" aria-labelledby="frequent-waste-title">
                            <h2 id="frequent-waste-title" class="section-title">Sampah Paling Sering Disetorkan</h2>

                            @if ($statistics['categories'] !== [])
                                <div class="rank-grid">
                                    @foreach ($statistics['categories'] as $index => $category)
                                        <article class="rank-card{{ $index === 0 ? ' rank-card-primary' : ($index < 3 ? ' rank-card-side' : ' rank-card-wide') }}">
                                            <div class="rank-category">
                                                <span class="rank-color" style="background: {{ $category['color'] }};">
                                                    <img src="{{ asset('icons/'.$category['icon']) }}" alt="">
                                                </span>
                                                <span class="rank-name">
                                                    {{ $category['name'] }}
                                                    @if ($index > 0 && $index < 3)
                                                        <span class="rank-count">{{ number_format($category['count'], fmod($category['count'], 1) === 0.0 ? 0 : 1, ',', '.') }} item</span>
                                                    @endif
                                                </span>
                                            </div>
                                            @if ($index === 0)
                                                <div class="rank-metric">
                                                    <strong>{{ number_format($category['count'], fmod($category['count'], 1) === 0.0 ? 0 : 1, ',', '.') }}</strong>
                                                    <span>Item</span>
                                                </div>
                                            @elseif ($index > 2)
                                                <span class="rank-count">{{ number_format($category['count'], fmod($category['count'], 1) === 0.0 ? 0 : 1, ',', '.') }} Item</span>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="stats-empty">Belum ada kategori setoran untuk ditampilkan.</div>
                            @endif
                        </section>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <button type="button" class="wa-button" aria-label="WhatsApp belum dikonfigurasi" title="Kontak WhatsApp resmi belum dikonfigurasi" disabled>
        <span>WA</span>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button');
            const panels = document.querySelectorAll('.dashboard-panel');
            const tabNav = document.getElementById('dashboard-tabs');
            const initialTarget = new URLSearchParams(window.location.search).get('tab') === 'statistics'
                ? 'statistik-panel'
                : 'home-panel';

            tabs.forEach(function (tab) {
                const isActive = tab.dataset.target === initialTarget;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            panels.forEach(function (panel) {
                const isActive = panel.id === initialTarget;
                panel.classList.toggle('is-active', isActive);
                panel.hidden = !isActive;
            });

            tabNav.classList.toggle('statistics-active', initialTarget === 'statistik-panel');
            tabNav.classList.add('ready');

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    const target = this.dataset.target;
                    const activeTab = document.querySelector('.tab-button.is-active');
                    const isMovingForward = Array.from(tabs).indexOf(tab) > Array.from(tabs).indexOf(activeTab);

                    tabs.forEach(function (item) {
                        item.classList.toggle('is-active', item === tab);
                        item.setAttribute('aria-selected', item === tab ? 'true' : 'false');
                    });

                    panels.forEach(function (panel) {
                        panel.classList.toggle('is-active', panel.id === target);
                        panel.hidden = panel.id !== target;
                    });

                    const targetPanel = document.getElementById(target);
                    targetPanel.classList.remove('panel-switch-forward', 'panel-switch-back');
                    void targetPanel.offsetWidth;
                    targetPanel.classList.add(isMovingForward ? 'panel-switch-forward' : 'panel-switch-back');

                    tabNav.classList.toggle('statistics-active', target === 'statistik-panel');

                    const url = new URL(window.location.href);
                    if (target === 'statistik-panel') {
                        url.searchParams.set('tab', 'statistics');
                    } else {
                        url.searchParams.delete('tab');
                    }
                    window.history.replaceState({}, '', url);
                });
            });


            const toggleButton = document.getElementById('toggle-balance');
            const balanceValue = document.getElementById('balance-value');
            let hidden = false;

            if (toggleButton && balanceValue) {
                toggleButton.addEventListener('click', function () {
                    hidden = !hidden;
                    const value = hidden ? '••••••' : '{{ number_format($balance, 0, ',', '.') }}';
                    balanceValue.textContent = value;
                    toggleButton.setAttribute('aria-label', hidden ? 'Tampilkan saldo' : 'Sembunyikan saldo');
                });
            }
        });
    </script>
</body>
</html>
