<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2F8961">
    <title>@yield('title', 'Data Warga') - E-Sabilillah</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Poppins, Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #17231d;
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
        input,
        select {
            font: inherit;
        }

        a {
            color: inherit;
        }

        .admin-sidebar {
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
            transition: transform 180ms ease;
        }

        .admin-brand {
            display: flex;
            min-height: 112px;
            align-items: center;
            justify-content: center;
            padding: 0 5px 16px;
        }

        .admin-brand img {
            display: block;
            width: 150px;
            max-height: 100px;
            object-fit: contain;
        }

        .admin-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .admin-nav-link,
        .admin-nav-heading {
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

        .admin-nav-link.active {
            font-weight: 650;
        }

        .admin-nav-link.active::before {
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: -12px;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: #fff;
            content: "";
        }

        .admin-nav-icon {
            display: block;
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            object-fit: contain;
        }

        .admin-nav-group {
            border-radius: 8px;
        }

        .admin-nav-heading {
            width: 100%;
            cursor: pointer;
            list-style: none;
        }

        .admin-nav-heading::-webkit-details-marker {
            display: none;
        }

        .admin-chevron {
            width: 18px;
            height: 18px;
            margin-left: auto;
            transition: transform 160ms ease;
        }

        .admin-chevron::after {
            display: block;
            width: 7px;
            height: 7px;
            margin: 2px auto 0;
            border-right: 1.5px solid currentColor;
            border-bottom: 1.5px solid currentColor;
            content: "";
            transform: rotate(45deg);
        }

        .admin-nav-group[open] .admin-chevron {
            transform: rotate(180deg);
        }

        .admin-subnav {
            display: grid;
            grid-template-rows: 0fr;
            padding: 0 0 0 34px;
            transition: grid-template-rows 260ms cubic-bezier(0.2, 0.75, 0.25, 1), padding 260ms ease;
        }

        .admin-nav-group[open] .admin-subnav {
            grid-template-rows: 1fr;
            padding-top: 2px;
            padding-bottom: 6px;
        }

        .admin-subnav-inner {
            min-height: 0;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-5px);
            transition: opacity 180ms ease, transform 220ms ease;
        }

        .admin-nav-group[open] .admin-subnav-inner {
            opacity: 1;
            transform: translateY(0);
        }

        .admin-subnav-item {
            position: relative;
            display: block;
            padding: 8px 5px;
            color: rgb(255 255 255 / 76%);
            font-size: 12px;
            text-align: center;
            text-decoration: none;
        }

        .admin-subnav-item.active {
            color: #fff;
            font-weight: 650;
        }

        .admin-subnav-item.active::before {
            position: absolute;
            top: 6px;
            bottom: 6px;
            left: calc(50% - 42px);
            width: 2px;
            border-radius: 2px;
            background: #fff;
            content: "";
        }

        .admin-subnav-item.disabled,
        .admin-nav-link.disabled {
            color: rgb(255 255 255 / 72%);
        }

        .admin-sidebar-bottom {
            margin-top: auto;
            padding-top: 20px;
        }

        .admin-logout {
            display: flex;
            width: 100%;
            min-height: 40px;
            align-items: center;
            gap: 10px;
            padding: 0 8px;
            border: 0;
            background: transparent;
            color: #F0442D;
            font-size: 13px;
            cursor: pointer;
        }

        .admin-logout-icon {
            width: 16px;
            height: 16px;
            background: currentColor;
            -webkit-mask: url('{{ asset('icons/logout-icon.png') }}') center / contain no-repeat;
            mask: url('{{ asset('icons/logout-icon.png') }}') center / contain no-repeat;
        }

        .admin-main {
            min-width: 0;
            min-height: 100vh;
            margin-left: 220px;
            padding: 32px 38px;
        }

        .admin-mobile-top {
            display: none;
        }

        .admin-backdrop {
            display: none;
        }

        @media (max-width: 1250px) and (min-width: 761px) {
            .admin-sidebar {
                width: 82px;
                align-items: center;
                padding: 28px 10px 16px;
            }

            .admin-brand {
                min-height: 96px;
                padding: 5px 0 20px;
            }

            .admin-brand img {
                width: 58px;
                max-height: 58px;
            }

            .admin-nav {
                width: 100%;
                align-items: center;
            }

            .admin-nav-link,
            .admin-nav-heading,
            .admin-logout {
                width: 52px;
                min-height: 48px;
                justify-content: center;
                gap: 0;
                padding: 0;
                overflow: hidden;
                font-size: 0;
            }

            .admin-nav-icon {
                width: 24px;
                height: 24px;
            }

            .admin-chevron,
            .admin-nav-link span,
            .admin-logout > span:not(.admin-logout-icon),
            .admin-nav-group .admin-subnav {
                display: none;
            }

            .admin-main {
                margin-left: 82px;
                padding: 22px;
            }
        }

        @media (max-width: 760px) {
            .admin-sidebar {
                width: min(240px, calc(100vw - 56px));
                transform: translateX(-105%);
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-backdrop.visible {
                position: fixed;
                z-index: 19;
                inset: 0;
                display: block;
                border: 0;
                background: rgb(17 33 24 / 42%);
            }

            .admin-main {
                margin-left: 0;
                padding: 16px;
            }

            .admin-mobile-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .admin-menu-toggle {
                display: flex;
                width: 40px;
                height: 40px;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                border: 0;
                border-radius: 8px;
                background: #fff;
                color: #2F8961;
                cursor: pointer;
            }

            .admin-menu-toggle span {
                width: 19px;
                height: 2px;
                border-radius: 2px;
                background: currentColor;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <button class="admin-backdrop" type="button" aria-label="Tutup menu"></button>

    <aside class="admin-sidebar" id="admin-sidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="E-Sabilillah Dashboard">
            <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung Zero Waste">
        </a>

        <nav class="admin-nav" aria-label="Navigasi admin">
            <a class="admin-nav-link" href="{{ route('admin.dashboard') }}">
                <img class="admin-nav-icon" src="{{ asset('icons/dashboard-icon.png') }}" alt="">
                <span>Dashboard</span>
            </a>

            <details class="admin-nav-group" open>
                <summary class="admin-nav-heading">
                    <img class="admin-nav-icon" src="{{ asset('icons/Keloladata-icon.png') }}" alt="">
                    <span>Kelola Data</span>
                    <span class="admin-chevron" aria-hidden="true"></span>
                </summary>
                <div class="admin-subnav">
                    <div class="admin-subnav-inner">
                        <a class="admin-subnav-item {{ request()->routeIs('admin.warga.*') ? 'active' : '' }}" href="{{ route('admin.warga.index') }}" @if (request()->routeIs('admin.warga.*')) aria-current="page" @endif>Data Warga</a>
                        <a class="admin-subnav-item {{ request()->routeIs('admin.petugas.*') ? 'active' : '' }}" href="{{ route('admin.petugas.index') }}" @if (request()->routeIs('admin.petugas.*')) aria-current="page" @endif>Data Petugas</a>
                    </div>
                </div>
            </details>

            <a class="admin-nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}" href="{{ route('admin.transaksi.index') }}" @if (request()->routeIs('admin.transaksi.*')) aria-current="page" @endif>
                <img class="admin-nav-icon" src="{{ asset('icons/Cart-icon.png') }}" alt="">
                <span>Transaksi Sampah</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.show') }}" @if (request()->routeIs('admin.profile.*')) aria-current="page" @endif>
                <img class="admin-nav-icon" src="{{ asset('icons/Profile-icon.png') }}" alt="">
                <span>Profile</span>
            </a>
        </nav>

        <div class="admin-sidebar-bottom">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="admin-logout" type="submit">
                    <span class="admin-logout-icon" aria-hidden="true"></span>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-mobile-top">
            <button class="admin-menu-toggle" type="button" aria-label="Buka menu" aria-controls="admin-sidebar" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
        @yield('content')
    </main>

    <script>
        const sidebar = document.getElementById('admin-sidebar');
        const menuToggle = document.querySelector('.admin-menu-toggle');
        const backdrop = document.querySelector('.admin-backdrop');

        if (menuToggle && backdrop) {
            function setSidebarOpen(isOpen) {
                sidebar.classList.toggle('open', isOpen);
                backdrop.classList.toggle('visible', isOpen);
                menuToggle.setAttribute('aria-expanded', String(isOpen));
            }

            menuToggle.addEventListener('click', () => setSidebarOpen(!sidebar.classList.contains('open')));
            backdrop.addEventListener('click', () => setSidebarOpen(false));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setSidebarOpen(false);
                }
            });
        }
    </script>
</body>
</html>
