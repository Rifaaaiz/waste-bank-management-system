<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2F8961">
    <title>Dashboard Admin - E-Sabilillah</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Poppins, Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #101010;
            background: #E5F4F0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            background: #E5F4F0;
        }

        button,
        select {
            font: inherit;
        }

        a {
            color: inherit;
        }

        .nav-icon {
            display: block;
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            object-fit: contain;
        }

        .dashboard {
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            z-index: 20;
            inset: 0 auto 0 0;
            display: flex;
            width: 220px;
            flex-direction: column;
            padding: 18px 12px 16px;
            overflow-y: auto;
            background: #2F8961;
            color: #fff;
            transition: transform 180ms ease, width 180ms ease;
        }

        .sidebar-brand {
            display: flex;
            min-height: 112px;
            align-items: center;
            justify-content: center;
            padding: 0 5px 16px;
        }

        .sidebar-brand img {
            display: block;
            width: 150px;
            max-height: 100px;
            object-fit: contain;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-link,
        .nav-heading {
            position: relative;
            display: flex;
            min-height: 41px;
            align-items: center;
            gap: 11px;
            padding: 0 8px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            text-align: left;
            text-decoration: none;
        }

        .nav-link,
        .nav-heading,
        .subnav-item,
        .logout-button {
            transition: transform 180ms ease, color 180ms ease, background-color 180ms ease;
        }

        .nav-link:hover,
        .nav-heading:hover,
        .subnav-item:hover,
        .logout-button:hover {
            transform: translateX(2px);
        }

        .nav-link:active,
        .nav-heading:active,
        .subnav-item:active,
        .logout-button:active {
            transform: scale(0.98);
        }

        .nav-icon {
            display: block;
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            object-fit: contain;
        }

        .nav-link .nav-icon,
        .nav-heading .nav-icon {
            transition: transform 220ms ease;
        }

        .nav-link:hover .nav-icon,
        .nav-link:focus-visible .nav-icon,
        .nav-heading:hover .nav-icon,
        .nav-heading:focus-visible .nav-icon,
        .nav-group.is-open .nav-heading .nav-icon {
            transform: scale(1.08);
        }

        .nav-link.active {
            background: transparent;
            font-weight: 650;
        }

        .nav-link.active::before {
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: -12px;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: #fff;
            content: "";
            animation: dashboard-active-indicator 220ms ease both;
        }

        @keyframes dashboard-active-indicator {
            from {
                opacity: 0;
                transform: scaleY(0.55);
            }

            to {
                opacity: 1;
                transform: scaleY(1);
            }
        }

        .nav-group {
            border-radius: 8px;
        }

        .nav-group .subnav {
            max-height: 0;
            overflow: hidden;
            padding: 0 0 0 34px;
            opacity: 0;
            transform: translateY(-8px);
            transition: max-height 420ms ease-in-out, padding 320ms ease-in-out, opacity 320ms ease-in-out, transform 320ms ease-in-out;
        }

        .nav-group.is-open .subnav {
            max-height: 120px;
            padding-top: 2px;
            padding-bottom: 6px;
            opacity: 1;
            transform: translateY(0);
        }

        .subnav-inner {
            min-height: 0;
            overflow: hidden;
        }

        .nav-heading {
            width: 100%;
            cursor: pointer;
            font: inherit;
        }

        .nav-heading .chevron {
            display: block;
            width: 18px;
            height: 18px;
            margin-left: auto;
            transition: transform 320ms ease-in-out;
            filter: brightness(0) invert(1);
            object-fit: contain;
            transform: rotate(180deg);
        }

        .nav-group.is-open .chevron {
            transform: rotate(270deg);
        }

        .subnav {
            gap: 0;
        }

        .subnav-item {
            display: block;
            padding: 8px 5px;
            color: rgb(255 255 255 / 68%);
            font-size: 13px;
            line-height: 1.4;
            text-align: center;
        }

        a.subnav-item {
            color: #fff;
            text-decoration: none;
        }

        a.subnav-item:hover,
        a.subnav-item:focus-visible {
            color: #DDF2E5;
            text-decoration: underline;
        }

        .subnav-item small {
            display: block;
            color: rgb(255 255 255 / 50%);
            font-size: 10px;
        }

        .nav-link.unavailable {
            color: rgb(255 255 255 / 72%);
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 20px;
        }

        .logout-button {
            display: flex;
            width: 100%;
            min-height: 40px;
            align-items: center;
            gap: 10px;
            padding: 0 8px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: #F0442D;
            font-size: 13px;
            cursor: pointer;
            transition: color 160ms ease, transform 160ms ease;
        }

        .logout-button:hover,
        .logout-button:focus-visible {
            color: #FF6A52;
        }

        .logout-button:active {
            transform: translateX(2px);
        }

        .logout-icon {
            display: block;
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
            background-color: currentColor;
            -webkit-mask: url('{{ asset('icons/logout-icon.png') }}') center / contain no-repeat;
            mask: url('{{ asset('icons/logout-icon.png') }}') center / contain no-repeat;
        }

        .main {
            min-width: 0;
            min-height: 100vh;
            margin-left: 220px;
            padding: 27px;
        }

        .menu-toggle,
        .sidebar-backdrop {
            display: none;
        }

        .menu-toggle span {
            display: block;
            width: 19px;
            height: 2px;
            border-radius: 2px;
            background: currentColor;
        }

        .menu-toggle {
            flex-direction: column;
            justify-content: center;
            gap: 4px;
        }

        .transaction-menu-toggle {
            margin-left: auto;
        }

        .welcome-banner {
            position: relative;
            display: flex;
            min-height: 210px;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            padding: 28px 38px;
            border-radius: 9px;
            background: #2F8961;
            color: #fff;
        }

        .welcome-copy {
            position: relative;
            z-index: 1;
            max-width: 560px;
        }

        .welcome-copy h1 {
            margin: 0 0 12px;
            font-size: clamp(24px, 2.1vw, 29px);
            font-weight: 700;
            line-height: 1.25;
        }

        .welcome-copy p {
            max-width: 390px;
            margin: 0;
            color: rgb(255 255 255 / 90%);
            font-size: 14px;
            line-height: 1.65;
        }

        .banner-illustration {
            position: absolute;
            right: 22px;
            bottom: 0;
            width: min(35%, 300px);
            height: auto;
            max-height: 100%;
            object-fit: contain;
            object-position: right bottom;
            opacity: 0.88;
            -webkit-mask-image: linear-gradient(90deg, transparent, #000 22%);
            mask-image: linear-gradient(90deg, transparent, #000 22%);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-top: 20px;
        }

        .stat-card {
            display: flex;
            min-height: 144px;
            align-items: center;
            gap: 14px;
            padding: 20px 17px;
            border-radius: 8px;
            color: #fff;
        }

        .stat-card.green {
            background: #2F8961;
        }

        .stat-card.blue {
            background: #05B8E8;
        }

        .stat-card.coral {
            background: #FF654E;
        }

        .stat-icon {
            display: grid;
            width: 47px;
            height: 47px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 10px;
            background: rgb(0 0 0 / 13%);
        }

        .stat-icon img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .stat-copy {
            min-width: 0;
        }

        .stat-value {
            display: block;
            margin-bottom: 4px;
            font-size: clamp(21px, 2vw, 28px);
            font-weight: 700;
            line-height: 1.15;
            overflow-wrap: anywhere;
        }

        .stat-label {
            display: block;
            color: rgb(255 255 255 / 93%);
            font-size: 13px;
            line-height: 1.4;
        }

        .status-card {
            align-items: flex-start;
            gap: 12px;
        }

        .status-copy h2 {
            margin: 0 0 7px;
            font-size: 16px;
        }

        .status-copy p {
            margin: 0 0 10px;
            color: rgb(255 255 255 / 88%);
            font-size: 11px;
            line-height: 1.45;
        }

        .status-chip {
            display: inline-flex;
            padding: 5px 9px;
            border: 1px solid rgb(255 255 255 / 32%);
            border-radius: 99px;
            color: #fff;
            font-size: 10px;
            line-height: 1.3;
        }

        .section-title {
            margin: 27px 0 4px;
            color: #101010;
            font-size: 24px;
            font-weight: 700;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr) minmax(0, 0.9fr);
            align-items: stretch;
            gap: 15px;
        }

        .panel {
            min-width: 0;
            min-height: 302px;
            padding: 10px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 18px rgb(34 79 60 / 5%);
        }

        .monthly-panel,
        .activity-panel {
            padding: 20px;
        }

        .monthly-panel {
            padding-right: 31px;
            padding-left: 31px;
        }

        .transactions-panel {
            display: flex;
            flex-direction: column;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 15px;
        }

        .transactions-panel .panel-heading {
            display: none;
        }

        .monthly-panel .panel-heading,
        .activity-panel .panel-heading {
            justify-content: center;
            margin-bottom: 18px;
            text-align: center;
        }

        .panel-heading h2 {
            margin: 0;
            color: #1b2921;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
        }

        .monthly-panel .panel-heading h2,
        .activity-panel .panel-heading h2 {
            color: #203b31;
            font-size: 18px;
            font-weight: 650;
        }

        .muted {
            color: #829087;
            font-size: 11px;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .transactions-panel .table-wrap {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 11px 8px;
            border-bottom: 1px solid #edf1ee;
            font-size: 11px;
            white-space: nowrap;
        }

        th {
            color: #77837c;
            font-size: 10px;
            font-weight: 600;
        }

        td {
            color: #35433b;
        }

        .resident-name {
            max-width: 135px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 7px 4px;
            border: 0;
            color: #101010;
            font-size: 13px;
        }

        .transactions-table td:first-child {
            width: 30px;
            font-size: 16px;
        }

        .transaction-person {
            display: grid;
            gap: 2px;
            white-space: normal;
        }

        .transaction-id {
            color: #203b31;
            font-size: 10px;
        }

        .transactions-table .resident-name {
            max-width: 155px;
            font-size: 13px;
            font-weight: 550;
        }

        .transactions-table td:nth-last-child(-n + 2) {
            text-align: right;
        }

        .transaction-amount,
        .transaction-date {
            white-space: nowrap;
        }

        .transaction-amount {
            font-size: 12px !important;
            font-weight: 600;
        }

        .transaction-date {
            font-size: 11px !important;
        }

        .status-badge {
            display: inline-flex;
            padding: 4px 7px;
            border-radius: 99px;
            background: #edf5f0;
            color: #47745b;
            font-size: 9px;
        }

        .status-badge.cancelled {
            background: #fff0ed;
            color: #ba5544;
        }

        .empty-state {
            padding: 25px 10px;
            color: #7d8b82;
            font-size: 9px;
            line-height: 1.6;
            text-align: center;
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 13px;
        }

        .pagination-summary {
            color: #758179;
            font-size: 10px;
        }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .page-link {
            display: grid;
            min-width: 27px;
            height: 27px;
            place-items: center;
            padding: 0 6px;
            border: 1px solid #e8efeb;
            border-radius: 6px;
            color: #506257;
            font-size: 10px;
            text-decoration: none;
        }

        .page-link.current {
            border-color: #2F8961;
            background: #2F8961;
            color: #fff;
        }

        .page-link img {
            display: block;
            width: 14px;
            height: 14px;
            object-fit: contain;
        }

        .page-link.next img {
            transform: rotate(180deg);
        }

        .page-link.disabled {
            color: #b6c0ba;
            pointer-events: none;
        }

        .period-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            max-width: 210px;
            margin: 0 auto 15px;
        }

        .period-summary {
            display: grid;
            gap: 3px;
            color: #151a17;
            text-align: center;
        }

        .period-summary strong {
            font-size: 11px;
            font-weight: 500;
        }

        .period-summary span {
            font-size: 10px;
            white-space: nowrap;
        }

        .period-arrow {
            display: grid;
            width: 28px;
            height: 28px;
            flex: 0 0 auto;
            place-items: center;
            border: 0;
            border-radius: 50%;
            background: transparent;
            cursor: pointer;
        }

        .period-arrow:hover {
            background: #edf6f1;
        }

        .period-arrow img {
            display: block;
            width: 18px;
            height: 18px;
        }

        .period-arrow.next img {
            transform: rotate(180deg);
        }

        .category-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .category-table th {
            padding: 0 0 7px;
            color: #151a17;
            font-size: 11px;
            font-weight: 600;
        }

        .category-table th:last-child,
        .category-table td:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .category-table td {
            padding: 4px 0;
            border: 0;
            color: #858585;
            font-size: 11px;
            font-weight: 600;
        }

        .category-name {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .category-color {
            width: 12px;
            height: 12px;
            flex: 0 0 auto;
            border-radius: 4px;
            background: var(--category-color);
        }

        .activity-panel {
            display: flex;
            flex-direction: column;
        }

        .activity-panel .panel-heading {
            margin-bottom: 5px;
        }

        .activity-subtitle {
            margin: 0 0 14px;
            color: #829087;
            font-size: 10px;
            text-align: center;
        }

        .activity-list {
            display: grid;
            align-content: start;
        }

        .activity-item {
            display: grid;
            grid-template-columns: 18px minmax(0, 1fr);
            align-items: start;
            gap: 8px;
            padding: 0 0 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid #dce5df;
        }

        .activity-symbol {
            display: block;
            width: 24px;
            height: 24px;
            object-fit: contain;
            filter: brightness(0) saturate(100%);
        }

        .activity-copy h3 {
            margin: 0 0 5px;
            color: #203b31;
            font-size: 12px;
            font-weight: 650;
            line-height: 1.4;
        }

        .activity-copy p {
            margin: 0 0 5px;
            color: #78857d;
            font-size: 10px;
            line-height: 1.5;
        }

        .activity-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 10px;
            color: #78857d;
            font-size: 12px;
            line-height: 1.45;
        }

        .activity-empty {
            padding: 15px 4px;
            color: #7d8b82;
            font-size: 12px;
            line-height: 1.6;
            text-align: center;
        }

        .panel-footer {
            margin-top: auto;
            padding: 12px 0 0 8px;
            color: #2F8961;
            font-size: 14px;
        }

        .panel-footer a {
            color: #2F8961;
            font-weight: 600;
            text-decoration: none;
        }

        .panel-footer a:hover {
            text-decoration: underline;
        }

        .unavailable-link {
            color: #829087;
            cursor: not-allowed;
        }

        @media (max-width: 1250px) and (min-width: 761px) {
            .sidebar {
                width: 82px;
                align-items: center;
                padding-right: 10px;
                padding-left: 10px;
                padding-top: 28px;
            }

            .sidebar-brand {
                min-height: 96px;
                align-items: center;
                padding: 5px 0 20px;
            }

            .sidebar-brand img {
                width: 58px;
                max-height: 58px;
            }

            .nav {
                width: 100%;
                align-items: center;
            }

            .nav-link,
            .nav-heading,
            .logout-button {
                width: 52px;
                min-height: 48px;
                justify-content: center;
                gap: 0;
                padding: 0;
                overflow: hidden;
                font-size: 0;
            }

            .nav-icon {
                width: 24px;
                height: 24px;
            }

            .nav-heading .chevron,
            .nav-link span,
            .logout-button span,
            .nav-group .subnav {
                display: none;
            }

            .nav-link.active {
                width: 62px;
                font-size: 10px;
                justify-content: center;
            }

            .nav-link.active span {
                display: block;
            }

            .main {
                margin-left: 82px;
                padding: 22px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr) minmax(0, 0.9fr);
            }
        }

        @media (max-width: 1000px) and (min-width: 761px) {
            .content-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .transactions-panel {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 760px) {
            .sidebar {
                width: min(240px, calc(100vw - 56px));
                transform: translateX(-105%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-backdrop.visible {
                position: fixed;
                z-index: 19;
                inset: 0;
                display: block;
                border: 0;
                background: rgb(17 33 24 / 42%);
            }

            .main {
                margin-left: 0;
                padding: 15px;
            }

            .menu-toggle {
                display: flex;
                width: 38px;
                height: 38px;
                flex: 0 0 auto;
                align-items: center;
                border: 0;
                border-radius: 8px;
                background: #fff;
                color: #2F8961;
                cursor: pointer;
            }

            .welcome-banner {
                min-height: 190px;
                padding: 25px 23px;
            }

            .banner-illustration {
                width: 42%;
                opacity: 0.58;
            }

            .welcome-copy {
                max-width: 80%;
            }

            .welcome-copy p {
                max-width: 330px;
                font-size: 13px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 11px;
                margin-top: 14px;
            }

            .stat-card {
                min-height: 118px;
                gap: 10px;
                padding: 14px 12px;
            }

            .stat-icon {
                width: 39px;
                height: 39px;
            }

            .status-card {
                grid-column: 1 / -1;
                min-height: 105px;
            }

            .section-title {
                margin-top: 23px;
                font-size: 21px;
            }

            .content-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 13px;
            }

            .transactions-panel {
                grid-column: auto;
            }

            .panel {
                min-height: 0;
                padding: 20px;
            }

            .monthly-panel {
                padding-right: 31px;
                padding-left: 31px;
            }

            .transaction-menu-toggle {
                margin-bottom: 10px;
            }

            .transactions-table {
                min-width: 430px;
            }

            .monthly-panel,
            .activity-panel {
                min-height: 300px;
            }

            .activity-panel {
                grid-column: auto;
            }

            .monthly-panel .panel-heading h2,
            .activity-panel .panel-heading h2 {
                font-size: 18px;
            }

            .category-table th,
            .category-table td {
                font-size: 12px;
            }

            .activity-copy h3 {
                font-size: 13px;
            }

            .activity-copy p {
                font-size: 11px;
            }
        }

        @media (max-width: 420px) {
            .main {
                padding: 12px;
            }

            .welcome-banner {
                min-height: 180px;
                padding: 20px 17px;
            }

            .welcome-copy {
                max-width: 88%;
            }

            .banner-illustration {
                right: 12px;
                width: min(42%, 140px);
                opacity: 0.42;
            }

            .stat-card {
                align-items: flex-start;
                flex-direction: column;
                gap: 9px;
            }

            .status-card {
                flex-direction: row;
                align-items: center;
            }

            th,
            td {
                padding-right: 10px;
                padding-left: 10px;
            }

            .pagination {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @include('admin.partials.logout-confirmation-styles')
</head>
<body>
    @php
        $isKelolaDataOpen = request()->routeIs('admin.warga.*', 'admin.petugas.*');
    @endphp

    <div class="dashboard">
        <button class="sidebar-backdrop" type="button" aria-label="Tutup menu"></button>

        <aside class="sidebar" id="admin-sidebar">
            <a class="sidebar-brand" href="{{ route('admin.dashboard') }}" aria-label="E-Sabilillah Dashboard">
                <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung Zero Waste">
            </a>

            <nav class="nav" aria-label="Navigasi admin">
                <a class="nav-link active" href="{{ route('admin.dashboard') }}" aria-current="page">
                    <img class="nav-icon" src="{{ asset('icons/dashboard-icon.png') }}" alt="">
                    <span>Dashboard</span>
                </a>

                <div class="nav-group{{ $isKelolaDataOpen ? ' is-open' : '' }}">
                    <button class="nav-heading" type="button" aria-expanded="{{ $isKelolaDataOpen ? 'true' : 'false' }}" aria-controls="dashboard-data-subnav">
                        <img class="nav-icon" src="{{ asset('icons/Keloladata-icon.png') }}" alt="">
                        <span>Kelola Data</span>
                        <img class="chevron" src="{{ asset('icons/arrow-icon.png') }}" alt="" aria-hidden="true">
                    </button>
                    <div class="subnav" id="dashboard-data-subnav" aria-hidden="{{ $isKelolaDataOpen ? 'false' : 'true' }}" @unless ($isKelolaDataOpen) inert @endunless>
                        <div class="subnav-inner">
                            <a class="subnav-item" href="{{ route('admin.warga.index') }}">Data Warga</a>
                            <a class="subnav-item" href="{{ route('admin.petugas.index') }}">Data Petugas</a>
                        </div>
                    </div>
                </div>

                <a class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}" href="{{ route('admin.transaksi.index') }}" @if (request()->routeIs('admin.transaksi.*')) aria-current="page" @endif>
                    <img class="nav-icon" src="{{ asset('icons/Cart-icon.png') }}" alt="">
                    <span>Transaksi Sampah</span>
                </a>
                <a class="nav-link{{ request()->routeIs('admin.profile.*') ? ' active' : '' }}" href="{{ route('admin.profile.show') }}" @if (request()->routeIs('admin.profile.*')) aria-current="page" @endif>
                    <img class="nav-icon" src="{{ asset('icons/Profile-icon.png') }}" alt="">
                    <span>Profile</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                @include('admin.partials.logout-confirmation', [
                    'logoutButtonClass' => 'logout-button',
                    'logoutIconClass' => 'logout-icon',
                ])
            </div>
        </aside>

        <main class="main">
            <button class="menu-toggle transaction-menu-toggle" type="button" aria-label="Buka menu" aria-controls="admin-sidebar" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            <section class="welcome-banner" aria-labelledby="welcome-title">
                <div class="welcome-copy">
                    <h1 id="welcome-title">Hello, {{ $admin->full_name ?: $admin->username }}!</h1>
                    <p>Selamat datang kembali! Saatnya melangkah ke dalam keseharian dengan semangat yang membara.</p>
                </div>
                <img class="banner-illustration" src="{{ asset('icons/Ilusdashboard-admin.png') }}" alt="" aria-hidden="true">
            </section>

            <section class="stats-grid" aria-label="Ringkasan data">
                <article class="stat-card green">
                    <span class="stat-icon"><img src="{{ asset('icons/Warga-icon.png') }}" alt=""></span>
                    <span class="stat-copy">
                        <strong class="stat-value">{{ number_format($totalCitizens) }}</strong>
                        <span class="stat-label">Total Warga</span>
                    </span>
                </article>

                <article class="stat-card blue">
                    <span class="stat-icon"><img src="{{ asset('icons/Transaksi-icon.png') }}" alt=""></span>
                    <span class="stat-copy">
                        <strong class="stat-value">{{ number_format($totalTransactions) }}</strong>
                        <span class="stat-label">Total Transaksi</span>
                    </span>
                </article>

                <article class="stat-card coral">
                    <span class="stat-icon"><img src="{{ asset('icons/Beratsmph-icon.png') }}" alt=""></span>
                    <span class="stat-copy">
                        <strong class="stat-value">{{ number_format($totalWasteWeight, 2, ',', '.') }} kg</strong>
                        <span class="stat-label">Total Berat Sampah</span>
                    </span>
                </article>

                <article class="stat-card green status-card">
                    <span class="stat-copy status-copy">
                        <h2>Cek Status</h2>
                        <p>Status operasional belum tersedia di database.</p>
                        <span class="status-chip">Belum dikonfigurasi</span>
                    </span>
                </article>
            </section>

            <h2 class="section-title">Transaksi Terakhir</h2>

            <section class="content-grid" aria-label="Aktivitas dan statistik">
                <article class="panel transactions-panel">
                    <div class="panel-heading">
                        <span class="muted">Data transaksi database</span>
                    </div>

                    @if ($transactions->count())
                        <div class="table-wrap">
                            <table class="transactions-table">
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                            <td>
                                                <span class="transaction-person">
                                                    <span class="transaction-id">#{{ $transaction->transaction_id }}</span>
                                                    <span class="resident-name" title="{{ $transaction->full_name ?: $transaction->username }}">
                                                        {{ $transaction->full_name ?: $transaction->username }}
                                                    </span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="transaction-amount">RP. {{ number_format((float) $transaction->amount, 0, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                <span class="transaction-date">{{ \Illuminate\Support\Carbon::parse($transaction->transaction_date)->format('d - m - Y') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="empty-state">Belum ada transaksi di database.</p>
                    @endif

                    <div class="pagination">
                        <span class="pagination-summary">
                            Showing {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} From {{ $transactions->total() }}
                        </span>
                        @if ($transactions->lastPage() > 1)
                            <nav class="pagination-links" aria-label="Halaman transaksi">
                                @if ($transactions->onFirstPage())
                                    <span class="page-link disabled" aria-disabled="true"><img src="{{ asset('icons/arrow-icon.png') }}" alt=""></span>
                                @else
                                    <a class="page-link" href="{{ $transactions->previousPageUrl() }}" aria-label="Halaman sebelumnya"><img src="{{ asset('icons/arrow-icon.png') }}" alt=""></a>
                                @endif

                                @foreach ($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                                    @if ($page === $transactions->currentPage())
                                        <span class="page-link current" aria-current="page">{{ $page }}</span>
                                    @else
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($transactions->hasMorePages())
                                    <a class="page-link next" href="{{ $transactions->nextPageUrl() }}" aria-label="Halaman berikutnya"><img src="{{ asset('icons/arrow-icon.png') }}" alt=""></a>
                                @else
                                    <span class="page-link next disabled" aria-disabled="true"><img src="{{ asset('icons/arrow-icon.png') }}" alt=""></span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </article>

                <article class="panel monthly-panel">
                    <div class="panel-heading">
                        <h2>Total Sampah Perbulan</h2>
                    </div>

                    <form class="period-control" method="GET" action="{{ route('admin.dashboard') }}">
                        <a class="period-arrow" href="{{ route('admin.dashboard', ['month' => $previousPeriod->month, 'year' => $previousPeriod->year]) }}" aria-label="Periode sebelumnya">
                            <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
                        </a>
                        <div class="period-summary">
                            <strong>Periode</strong>
                            <span>{{ $period->copy()->startOfMonth()->format('j M Y') }} - {{ $period->copy()->endOfMonth()->format('j M Y') }}</span>
                        </div>
                        <a class="period-arrow next" href="{{ route('admin.dashboard', ['month' => $nextPeriod->month, 'year' => $nextPeriod->year]) }}" aria-label="Periode berikutnya">
                            <img src="{{ asset('icons/arrow-icon.png') }}" alt="">
                        </a>
                    </form>

                    @if ($monthlyCategories->isNotEmpty())
                        <table class="category-table" aria-label="Berat sampah selesai per kategori">
                            <thead>
                                <tr>
                                    <th scope="col">Jenis Sampah</th>
                                    <th scope="col">Bulan Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $categoryColors = ['#8BC324', '#7478E8', '#2D3C75', '#963737', '#1E4CE0', '#8A35E8'];
                            @endphp
                            @foreach ($monthlyCategories as $index => $category)
                                <tr>
                                    <td>
                                        <span class="category-name">
                                            <span class="category-color" style="--category-color: {{ $categoryColors[$index % count($categoryColors)] }}" aria-hidden="true"></span>
                                            {{ $category->category_name }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($category->total_weight, 2, ',', '.') }} kg</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="empty-state">Belum ada transaksi pada periode ini.</p>
                    @endif
                </article>

                <article class="panel activity-panel">
                    <div class="panel-heading">
                        <h2>Aktivitas &amp; notifikasi</h2>
                    </div>
                    <p class="activity-subtitle">Aktivitas terbaru dari database</p>

                    @if ($activities->isNotEmpty())
                        <div class="activity-list">
                            @foreach ($activities as $activity)
                                <article class="activity-item">
                                    <img
                                        class="activity-symbol"
                                        src="{{ asset(str_contains($activity['title'], 'pencairan') ? 'icons/Cart-icon.png' : 'icons/Keloladata-icon.png') }}"
                                        alt=""
                                        aria-hidden="true"
                                    >
                                    <div class="activity-copy">
                                        <h3>{{ $activity['title'] }}</h3>
                                        @if ($activity['reference'])
                                            <p>{{ $activity['reference'] }}@if ($activity['detail']) · @endif{{ $activity['detail'] }}</p>
                                        @elseif ($activity['detail'])
                                            <p>{{ $activity['detail'] }}</p>
                                        @endif
                                        <div class="activity-meta">
                                            <span>Waktu aktivitas: {{ \Illuminate\Support\Carbon::parse($activity['created_at'])->diffForHumans() }}</span>
                                            @if ($activity['status'])
                                                <span>{{ $activity['status'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="activity-empty">Belum ada aktivitas atau notifikasi di database.</p>
                    @endif

                </article>
            </section>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('admin-sidebar');
        const menuToggle = document.querySelector('.menu-toggle');
        const backdrop = document.querySelector('.sidebar-backdrop');
        const dataMenuToggle = sidebar.querySelector('.nav-heading');
        const dataSubnav = document.getElementById('dashboard-data-subnav');

        dataMenuToggle.addEventListener('click', () => {
            const isOpen = dataMenuToggle.getAttribute('aria-expanded') === 'true';
            const willOpen = !isOpen;
            dataMenuToggle.setAttribute('aria-expanded', String(willOpen));
            dataSubnav.setAttribute('aria-hidden', String(!willOpen));
            if (willOpen) {
                dataSubnav.removeAttribute('inert');
            } else {
                dataSubnav.setAttribute('inert', '');
            }
            dataMenuToggle.closest('.nav-group').classList.toggle('is-open', willOpen);
        });

        function setSidebarOpen(isOpen) {
            sidebar.classList.toggle('open', isOpen);
            backdrop.classList.toggle('visible', isOpen);
            menuToggle.setAttribute('aria-expanded', String(isOpen));
        }

        menuToggle.addEventListener('click', () => {
            setSidebarOpen(!sidebar.classList.contains('open'));
        });
        backdrop.addEventListener('click', () => setSidebarOpen(false));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        });
    </script>
</body>
</html>