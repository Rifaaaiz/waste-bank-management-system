@php($editing = isset($warga))

@once
    <style>
        .rt-dropdown { position: relative; }
        .rt-dropdown-native { display: block; }
        .rt-dropdown-control { display: none; }
        .js-ready .rt-dropdown-native { display: none; }
        .js-ready .rt-dropdown-control { display: block; }
        .rt-dropdown-trigger { display: flex; width: 100%; min-height: 48px; align-items: center; justify-content: space-between; gap: 12px; padding: 0 14px; border: 1px solid #dce5df; border-radius: 8px; background: #fff; color: #26372e; font: inherit; text-align: left; cursor: pointer; transition: border-color 160ms ease, box-shadow 160ms ease; }
        .rt-dropdown-trigger[aria-expanded="true"],
        .rt-dropdown-trigger:focus-visible { border-color: #2F8961; outline: none; box-shadow: 0 0 0 3px rgb(47 137 97 / 12%); }
        .rt-dropdown-placeholder { color: #9aa79f; }
        .rt-dropdown-icon { width: 16px; height: 16px; flex: 0 0 auto; object-fit: contain; transform: rotate(180deg); transition: transform 180ms ease; }
        .rt-dropdown-trigger[aria-expanded="true"] .rt-dropdown-icon { transform: rotate(90deg); }
        .rt-dropdown-menu { position: absolute; z-index: 10; top: calc(100% + 7px); right: 0; left: 0; max-height: 0; padding: 0 6px; overflow: hidden; visibility: hidden; border: 1px solid transparent; border-radius: 9px; background: #fff; opacity: 0; pointer-events: none; transform: translateY(-6px); transition: max-height 220ms ease, padding 220ms ease, opacity 160ms ease, transform 180ms ease, visibility 0s linear 220ms; }
        .rt-dropdown-menu.open { max-height: 320px; padding: 6px; overflow-y: auto; visibility: visible; border-color: #e2eae5; opacity: 1; pointer-events: auto; transform: translateY(0); box-shadow: 0 12px 26px rgb(34 79 60 / 13%); transition-delay: 0s; }
        .rt-dropdown-option { display: block; width: 100%; min-height: 40px; padding: 0 10px; border: 0; border-radius: 6px; background: transparent; color: #35463d; font: inherit; text-align: left; cursor: pointer; }
        .rt-dropdown-option:hover,
        .rt-dropdown-option:focus-visible,
        .rt-dropdown-option[aria-selected="true"] { outline: none; background: #edf6f1; color: #256f4e; }
        @media (prefers-reduced-motion: reduce) {
            .rt-dropdown-trigger,
            .rt-dropdown-icon,
            .rt-dropdown-menu { transition: none; }
        }
    </style>
@endonce

<form class="warga-form" method="POST" action="{{ $editing ? route('admin.warga.update', $warga->user_id) : route('admin.warga.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="form-grid">
        <div class="form-field form-field-wide">
            <label for="full_name">Nama Warga</label>
            <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $editing ? $warga->full_name : '') }}" placeholder="Masukan Nama Warga" maxlength="100" required>
            @error('full_name') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-field form-field-wide">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username', $editing ? $warga->username : '') }}" placeholder="Masukan Username" maxlength="50" autocomplete="username" required>
            @error('username') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-field form-field-wide">
            <label for="phone_number">Nomor Telepon</label>
            <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $editing ? $warga->phone_number : '') }}" placeholder="Masukan Nomor Telepon" maxlength="20" required>
            @error('phone_number') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-field form-field-wide">
            <label for="address">Alamat Jalan</label>
            <input id="address" name="address" type="text" value="{{ old('address', $editing ? $warga->address : '') }}" placeholder="Masukan Alamat Jalan" maxlength="255" required>
            @error('address') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-field form-field-wide">
            <label for="password">{{ $editing ? 'Password baru (opsional)' : 'Password' }}</label>
            <input id="password" name="password" type="password" placeholder="Masukan Password" autocomplete="new-password" minlength="8" maxlength="255" {{ $editing ? '' : 'required' }}>
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-field form-field-wide">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Konfirmasi Password" autocomplete="new-password" minlength="8" maxlength="255" {{ $editing ? '' : 'required' }}>
        </div>
        <div class="form-field">
            <label for="blok_rumah">Blok Rumah</label>
            <input id="blok_rumah" name="blok_rumah" type="text" value="{{ old('blok_rumah', $editing ? $warga->blok_rumah : '') }}" placeholder="Masukan Blok Rumah" maxlength="100" required>
            @error('blok_rumah') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
            <label for="rt">RT</label>
            @php($selectedRt = old('rt', $editing ? $warga->rt : ''))
            <div class="rt-dropdown">
                <select class="rt-dropdown-native" id="rt" name="rt" required>
                <option value="" disabled @selected(! in_array($selectedRt, $rtOptions, true))>Pilih RT</option>
                @foreach ($rtOptions as $rtOption)
                    <option value="{{ $rtOption }}" @selected($selectedRt === $rtOption)>RT {{ $rtOption }}</option>
                @endforeach
                </select>
                <div class="rt-dropdown-control" data-rt-dropdown>
                    <button class="rt-dropdown-trigger" type="button" role="combobox" aria-label="Pilih RT" aria-controls="rt-options" aria-haspopup="listbox" aria-expanded="false" aria-required="true">
                        <span class="{{ in_array($selectedRt, $rtOptions, true) ? '' : 'rt-dropdown-placeholder' }}">{{ in_array($selectedRt, $rtOptions, true) ? 'RT '.$selectedRt : 'Pilih RT' }}</span>
                        <img class="rt-dropdown-icon" src="{{ asset('icons/arrow-icon.png') }}" alt="">
                    </button>
                    <div class="rt-dropdown-menu" id="rt-options" role="listbox" aria-label="Pilih RT">
                        @foreach ($rtOptions as $rtOption)
                            <button class="rt-dropdown-option" type="button" role="option" data-value="{{ $rtOption }}" aria-selected="{{ $selectedRt === $rtOption ? 'true' : 'false' }}">RT {{ $rtOption }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('rt') <span class="field-error">{{ $message }}</span> @enderror

            <div class="form-actions">
                <a class="secondary-button" href="{{ route('admin.warga.index') }}">Batal</a>
                <button class="primary-button" type="submit">{{ $editing ? 'Simpan Perubahan' : 'Submit' }}</button>
            </div>
        </div>
    </div>
</form>

@once
    <script>
        document.documentElement.classList.add('js-ready');

        document.querySelectorAll('[data-rt-dropdown]').forEach((dropdown) => {
            const wrapper = dropdown.closest('.rt-dropdown');
            const select = wrapper.querySelector('.rt-dropdown-native');
            select.removeAttribute('required');
            const trigger = dropdown.querySelector('.rt-dropdown-trigger');
            const label = trigger.querySelector('span');
            const menu = dropdown.querySelector('.rt-dropdown-menu');
            const options = [...menu.querySelectorAll('[role="option"]')];

            function closeMenu(returnFocus = false) {
                menu.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
                if (returnFocus) trigger.focus();
            }

            function openMenu() {
                menu.classList.add('open');
                trigger.setAttribute('aria-expanded', 'true');
                (options.find((option) => option.dataset.value === select.value) || options[0]).focus();
            }

            function selectOption(option) {
                select.value = option.dataset.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                label.textContent = option.textContent.trim();
                label.classList.remove('rt-dropdown-placeholder');
                options.forEach((item) => item.setAttribute('aria-selected', String(item === option)));
                closeMenu(true);
            }

            trigger.addEventListener('click', () => {
                if (trigger.getAttribute('aria-expanded') === 'true') {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            trigger.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
                    event.preventDefault();
                    closeMenu();
                } else if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openMenu();
                }
            });

            options.forEach((option, index) => {
                option.addEventListener('click', () => selectOption(option));
                option.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        closeMenu(true);
                    } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();
                        const nextIndex = (index + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
                        options[nextIndex].focus();
                    } else if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        selectOption(option);
                    }
                });
            });

            document.addEventListener('click', (event) => {
                if (!dropdown.contains(event.target)) closeMenu();
            });
        });
    </script>
@endonce
