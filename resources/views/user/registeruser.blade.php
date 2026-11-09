<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2F8961">
    <title>Register - E-Sabilillah</title>
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

        .page {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0 auto;
        }

        .shell {
            display: flex;
            flex-direction: column;
            width: 100%;
            min-height: 100vh;
            min-height: 100svh;
            margin: 0 auto;
            overflow: hidden;
            background: #2F8961;
        }

        .hero {
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

        .panel {
            position: relative;
            z-index: 1;
            display: flex;
            flex: 1 0 auto;
            flex-direction: column;
            min-height: 540px;
            margin-top: -14px;
            padding: 23px 18px calc(18px + env(safe-area-inset-bottom));
            border-radius: 14px 14px 0 0;
            background: #2F8961;
            color: #fff;
        }

        .title-row {
            position: relative;
            display: flex;
            min-height: 34px;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        h1 {
            margin: 0;
            text-align: center;
            font-size: 25px;
            font-weight: 750;
            line-height: 1.2;
        }

        .back-button {
            position: absolute;
            left: 0;
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 1px solid rgb(255 255 255 / 75%);
            border-radius: 9px;
            color: #fff;
            font-size: 22px;
            line-height: 1;
            text-decoration: none;
        }

        .back-button:hover,
        .back-button:focus-visible {
            background: rgb(255 255 255 / 12%);
        }

        .validation-errors {
            margin: 0 0 12px;
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
            margin-bottom: 11px;
        }

        .field label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 600;
        }

        .field input {
            display: block;
            width: 100%;
            min-height: 40px;
            padding: 8px 12px;
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
            margin-top: 4px;
            color: #fff1ed;
            font-size: 12px;
        }

        .submit-button {
            display: flex;
            width: 100%;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
            border: 1px solid transparent;
            border-radius: 9px;
            background: #91C923;
            color: #fff;
            font-size: 18px;
            font-weight: 650;
            cursor: pointer;
            transition: background-color 120ms ease, transform 120ms ease;
        }

        .submit-button:hover,
        .submit-button:focus-visible {
            background: #83b91d;
        }

        .submit-button:active {
            transform: scale(0.99);
        }

        .submit-button:disabled {
            cursor: wait;
            opacity: 0.8;
        }

        .support {
            margin: auto 0 0;
            padding-top: 26px;
            color: #fff;
            font-size: 11px;
            line-height: 1.5;
            text-align: center;
        }

        .support span {
            color: #b6e779;
            font-weight: 650;
        }

        @media (min-width: 600px) {
            body {
                padding: 24px 0;
            }

            .page {
                display: flex;
                min-height: calc(100vh - 48px);
                min-height: calc(100svh - 48px);
                align-items: center;
                justify-content: center;
                padding: 0 20px;
            }

            .shell {
                width: min(100%, 420px);
                min-height: min(860px, calc(100vh - 48px));
                min-height: min(860px, calc(100svh - 48px));
                border-radius: 18px;
                box-shadow: 0 18px 54px rgb(23 61 43 / 18%);
            }

            .hero {
                flex-basis: clamp(250px, 34svh, 330px);
            }

            .panel {
                padding-right: 26px;
                padding-left: 26px;
                border-radius: 14px 14px 0 0;
            }
        }

        @media (max-height: 620px) {
            .hero {
                flex-basis: 190px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="shell" aria-labelledby="register-title">
            <header class="hero">
                <div class="brand">
                    <img src="{{ asset('images/LogoE-Sabililah.png') }}" alt="Logo E-Sabilillah">
                    <span>E-Sabilillah</span>
                </div>
            </header>

            <div class="panel">
                <div class="title-row">
                    <a class="back-button" href="{{ route('login') }}" aria-label="Kembali ke Login">&larr;</a>
                    <h1 id="register-title">Register</h1>
                </div>

                @if ($errors->any())
                    <div class="validation-errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="register-form" method="POST" action="{{ route('register.store') }}">
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
                        <label for="phone_number">Nomor HP</label>
                        <input
                            id="phone_number"
                            name="phone_number"
                            type="tel"
                            value="{{ old('phone_number') }}"
                            placeholder="Masukan Nomor HP"
                            autocomplete="tel"
                            inputmode="tel"
                            maxlength="20"
                            required
                            aria-invalid="{{ $errors->has('phone_number') ? 'true' : 'false' }}"
                        >
                        @error('phone_number')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
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
                        <label for="password_confirmation">Konfirmasi password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            placeholder="Masukan Konfirmasi password"
                            autocomplete="new-password"
                            minlength="8"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="address">Alamat rumah</label>
                        <input
                            id="address"
                            name="address"
                            type="text"
                            value="{{ old('address') }}"
                            placeholder="Masukan Alamat rumah"
                            autocomplete="street-address"
                            maxlength="255"
                            required
                            aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}"
                        >
                        @error('address')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="rt">RT</label>
                        <input
                            id="rt"
                            name="rt"
                            type="text"
                            value="{{ old('rt') }}"
                            placeholder="Masukan Nomor RT"
                            inputmode="numeric"
                            autocomplete="off"
                            maxlength="2"
                            pattern="[0-9]{2}"
                            required
                            aria-invalid="{{ $errors->has('rt') ? 'true' : 'false' }}"
                        >
                        @error('rt')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="submit-button" type="submit">
                        <span class="button-label">Kirim</span>
                    </button>
                </form>

                <p class="support">
                    Mengalami kendala?
                    <span>Hubungi kami melalui WhatsApp</span>
                </p>
            </div>
        </section>
    </main>
    <script>
        document.getElementById('register-form').addEventListener('submit', function () {
            const button = this.querySelector('.submit-button');

            if (this.checkValidity()) {
                button.disabled = true;
                button.querySelector('.button-label').textContent = 'Mengirim...';
            }
        });
    </script>
</body>
</html>