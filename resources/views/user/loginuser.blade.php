<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2f8961">
    <title>Login - E-Sabilillah</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
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
            min-width: 320px;
            min-height: 100%;
            margin: 0;
        }

        body {
            background: #eaf1ec;
        }

        button,
        input {
            font: inherit;
        }

        .login-page {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0 auto;
        }

        .login-shell {
            display: flex;
            flex-direction: column;
            width: 100%;
            min-height: 100vh;
            min-height: 100svh;
            margin: 0 auto;
            overflow: hidden;
            background: #2f8961;
        }

        .hero {
            position: relative;
            display: flex;
            flex: 0 0 clamp(230px, 33svh, 330px);
            align-items: flex-start;
            justify-content: center;
            background-image: linear-gradient(180deg, rgb(0 0 0 / 22%), transparent 58%), url("{{ asset('images/cewe2.png') }}");
            background-position: center 22%;
            background-size: cover;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: max(18px, env(safe-area-inset-top));
            padding: 6px 14px 6px 8px;
            border: 1px solid rgb(255 255 255 / 28%);
            border-radius: 999px;
            background: rgb(24 61 47 / 58%);
            color: #fff;
            font-size: 17px;
            font-weight: 750;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 3px rgb(0 0 0 / 30%);
        }

        .brand img {
            display: block;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
        }

        .login-panel {
            position: relative;
            z-index: 1;
            display: flex;
            flex: 1 0 auto;
            flex-direction: column;
            min-height: 480px;
            margin-top: -14px;
            padding: 24px 18px calc(18px + env(safe-area-inset-bottom));
            border-radius: 14px 14px 0 0;
            background: #2f8961;
            color: #fff;
        }

        h1 {
            margin: 0 0 22px;
            text-align: center;
            font-size: 26px;
            font-weight: 750;
            line-height: 1.2;
        }

        .validation-errors {
            margin: 0 0 16px;
            padding: 10px 12px;
            border: 1px solid rgb(255 255 255 / 45%);
            border-radius: 8px;
            background: rgb(120 25 25 / 22%);
            font-size: 13px;
        }

        .validation-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .field {
            margin-bottom: 15px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .field input {
            display: block;
            width: 100%;
            min-height: 42px;
            padding: 9px 12px;
            border: 1px solid transparent;
            border-radius: 9px;
            outline: none;
            background: #fff;
            color: #3e4742;
            font-size: 14px;
        }

        .field input::placeholder {
            color: #8b918e;
        }

        .field input:focus {
            border-color: #b6e779;
            box-shadow: 0 0 0 3px rgb(182 231 121 / 25%);
        }

        .field input[aria-invalid="true"] {
            border-color: #ffd0c9;
        }

        .field-error {
            display: block;
            margin-top: 5px;
            color: #fff1ed;
            font-size: 12px;
        }

        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: -7px;
            margin-bottom: 19px;
            font-size: 12px;
        }

        .forgot-link,
        .forgot-unavailable {
            color: #fff;
            text-decoration: none;
        }

        .forgot-link:hover,
        .forgot-link:focus-visible {
            color: #b6e779;
            text-decoration: underline;
        }

        .actions {
            display: grid;
            gap: 10px;
        }

        .button {
            display: flex;
            width: 100%;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 9px;
            font-size: 17px;
            font-weight: 650;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .button-login {
            background: #91c923;
            color: #fff;
        }

        .button-login:hover,
        .button-login:focus-visible {
            background: #83b91d;
        }

        .button-register {
            border-color: #fff;
            background: transparent;
            color: #b6e779;
        }

        .button-register:hover,
        .button-register:focus-visible {
            background: rgb(255 255 255 / 9%);
        }

        .button-register:disabled {
            cursor: not-allowed;
        }

        .support {
            margin: auto 0 0;
            padding-top: 30px;
            color: #fff;
            font-size: 11px;
            line-height: 1.5;
            text-align: center;
        }

        .support a {
            color: #b6e779;
            font-weight: 650;
            text-decoration: none;
        }

        .support a:hover,
        .support a:focus-visible {
            text-decoration: underline;
        }

        @media (min-width: 600px) {
            body {
                padding: 24px 0;
            }

            .login-page {
                display: flex;
                min-height: calc(100vh - 48px);
                min-height: calc(100svh - 48px);
                align-items: center;
                justify-content: center;
                padding: 0 20px;
            }

            .login-shell {
                width: min(100%, 420px);
                min-height: min(860px, calc(100vh - 48px));
                min-height: min(860px, calc(100svh - 48px));
                border-radius: 18px;
                box-shadow: 0 18px 54px rgb(23 61 43 / 18%);
            }

            .hero {
                flex-basis: clamp(250px, 34svh, 330px);
            }

            .login-panel {
                padding-right: 26px;
                padding-left: 26px;
                border-radius: 14px 14px 0 0;
            }
        }

        @media (max-height: 620px) {
            .hero {
                flex-basis: 190px;
            }

            .login-panel {
                min-height: 430px;
            }
        }
    </style>
</head>
<body>
    <main class="login-page">
        <section class="login-shell" aria-labelledby="login-title">
            <header class="hero">
                <div class="brand">
                    <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo E-Sabilillah">
                    <span>E-Sabilillah</span>
                </div>
            </header>

            <div class="login-panel">
                <h1 id="login-title">Login</h1>

                @if (session('status'))
                    <p class="validation-errors" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="validation-errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
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
                            required
                            aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                            @if ($errors->has('username')) aria-describedby="username-error" @endif
                        >
                        @error('username')
                            <span class="field-error" id="username-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Masukan Password"
                            autocomplete="current-password"
                            required
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            @if ($errors->has('password')) aria-describedby="password-error" @endif
                        >
                        @error('password')
                            <span class="field-error" id="password-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="forgot-row">
                        <a class="forgot-link" href="{{ route('password.request') }}">Lupa Password</a>
                    </div>

                    <div class="actions">
                        <button class="button button-login" type="submit">Login</button>
                        <a class="button button-register" href="{{ route('register') }}">Register</a>
                    </div>
                </form>

                <p class="support">
                    Mengalami kendala?
                    <span>Hubungi kami melalui WhatsApp</span>
                </p>
            </div>
        </section>
    </main>
</body>
</html>