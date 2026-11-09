<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2F8961">
    <title>Lupa Password Admin - E-Sabilillah</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Poppins, Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-width: 320px;
            min-height: 100%;
            margin: 0;
        }

        body {
            min-height: 100vh;
            min-height: 100svh;
        }

        button,
        input {
            font: inherit;
        }

        .reset-page {
            display: flex;
            min-height: 100vh;
            min-height: 100svh;
        }

        .reset-panel,
        .logo-panel {
            display: flex;
            width: 50%;
            min-width: 0;
            min-height: 100vh;
            min-height: 100svh;
            align-items: center;
            justify-content: center;
        }

        .reset-panel {
            padding: 48px clamp(36px, 5.2vw, 84px);
            background: #2F8961;
            color: #fff;
        }

        .reset-content {
            width: 100%;
            max-width: 570px;
            margin: auto;
        }

        .mobile-brand {
            display: none;
        }

        h1 {
            margin: 0;
            text-align: center;
            font-size: 36px;
            font-weight: 750;
            line-height: 1.2;
        }

        .subtitle {
            margin: 14px 0 34px;
            text-align: center;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
        }

        .notice,
        .errors {
            margin: 0 0 18px;
            padding: 11px 14px;
            border: 1px solid rgb(255 255 255 / 45%);
            border-radius: 7px;
            background: rgb(255 255 255 / 10%);
            font-size: 14px;
            line-height: 1.45;
        }

        .errors {
            background: rgb(120 25 25 / 22%);
        }

        .errors ul {
            margin: 0;
            padding-left: 20px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: 700;
        }

        .field input {
            display: block;
            width: 100%;
            min-height: 44px;
            padding: 10px 13px;
            border: 1px solid transparent;
            border-radius: 9px;
            outline: none;
            background: #fff;
            color: #3e4742;
            font-size: 15px;
        }

        .field input::placeholder {
            color: #898f8c;
        }

        .field input:focus {
            border-color: #A1CB35;
            box-shadow: 0 0 0 3px rgb(161 203 53 / 24%);
        }

        .field input[aria-invalid="true"] {
            border-color: #ffd0c9;
        }

        .field input[readonly] {
            background: #edf2ee;
        }

        .field-error {
            display: block;
            margin-top: 6px;
            color: #fff1ed;
            font-size: 13px;
        }

        .submit-button {
            display: flex;
            width: 100%;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 5px;
            background: #A1CB35;
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 140ms ease, transform 140ms ease;
        }

        .submit-button:hover,
        .submit-button:focus-visible {
            background: #91bb2d;
        }

        .submit-button:active {
            transform: scale(0.99);
        }

        .submit-button:disabled {
            cursor: wait;
            opacity: 0.8;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #e4f0d0;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
        }

        .back-link:hover,
        .back-link:focus-visible {
            color: #fff;
            text-decoration: underline;
        }

        .logo-panel {
            padding: 48px;
            background: #fff;
        }

        .logo-panel img {
            display: block;
            width: min(75%, 520px);
            max-height: 70vh;
            object-fit: contain;
        }

        @media (max-width: 900px) and (min-width: 701px) {
            .reset-panel {
                padding-right: 38px;
                padding-left: 38px;
            }

            .logo-panel {
                padding: 32px;
            }
        }

        @media (max-width: 700px) {
            .reset-panel {
                width: 100%;
                min-height: 100vh;
                min-height: 100svh;
                padding: 32px 24px;
            }

            .reset-content {
                max-width: 480px;
            }

            .mobile-brand {
                display: flex;
                justify-content: center;
                margin-bottom: 24px;
            }

            .mobile-brand img {
                display: block;
                width: min(42vw, 170px);
                max-height: 110px;
                object-fit: contain;
            }

            h1 {
                font-size: 32px;
            }

            .subtitle {
                margin: 10px 0 28px;
                font-size: 16px;
            }

            .logo-panel {
                display: none;
            }
        }

        @media (max-height: 620px) and (max-width: 700px) {
            .reset-panel {
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .mobile-brand {
                margin-bottom: 12px;
            }

            .mobile-brand img {
                max-height: 72px;
            }

            .subtitle {
                margin-bottom: 20px;
            }

            .field {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <main class="reset-page">
        <section class="reset-panel" aria-labelledby="reset-title">
            <div class="reset-content">
                <div class="mobile-brand">
                    <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung Zero Waste">
                </div>

                <h1 id="reset-title">Lupa Password</h1>
                <p class="subtitle">Pastikan anda ingat password baru anda !</p>

                @if (session('status'))
                    <p class="notice" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (isset($token))
                    <form id="admin-reset-form" method="POST" action="{{ route('admin.password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="field">
                            <label for="username">Username</label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username', $username ?? '') }}"
                                placeholder="Masukan Username"
                                autocomplete="username"
                                autocapitalize="none"
                                maxlength="50"
                                required
                                aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                            >
                            @error('username')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password">Password Baru</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Masukan Password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="255"
                                required
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            >
                            @error('password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Masukan Konfirmasi Password Baru"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="255"
                                required
                            >
                        </div>

                        <button class="submit-button" type="submit">
                            <span class="button-label">Sign In</span>
                        </button>
                    </form>
                @else
                    <form id="admin-reset-form" method="POST" action="{{ route('admin.password.email') }}">
                        @csrf

                        <div class="field">
                            <label for="username">Username</label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username') }}"
                                placeholder="Masukan Username"
                                autocomplete="username"
                                autocapitalize="none"
                                maxlength="50"
                                required
                                aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                            >
                            @error('username')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="email">Email terdaftar</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="Masukan Email Admin"
                                autocomplete="email"
                                maxlength="100"
                                required
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            >
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <button class="submit-button" type="submit">
                            <span class="button-label">Sign In</span>
                        </button>
                    </form>
                @endif

                <a class="back-link" href="{{ route('admin.login') }}">Kembali ke Login Admin</a>
            </div>
        </section>

        <aside class="logo-panel" aria-label="Logo organisasi">
            <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo KPP Bina Lindung Zero Waste">
        </aside>
    </main>
    <script>
        document.getElementById('admin-reset-form').addEventListener('submit', function () {
            const button = this.querySelector('.submit-button');

            if (this.checkValidity()) {
                button.disabled = true;
                button.querySelector('.button-label').textContent = 'Memproses...';
            }
        });
    </script>
</body>
</html>