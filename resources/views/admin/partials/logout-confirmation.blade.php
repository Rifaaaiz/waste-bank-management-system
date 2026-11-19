<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button class="{{ $logoutButtonClass }}" type="button" data-logout-confirmation-open>
        <span class="{{ $logoutIconClass }}" aria-hidden="true"></span>
        <span>Log Out</span>
    </button>
    <dialog class="logout-confirmation" id="admin-logout-confirmation" aria-labelledby="admin-logout-confirmation-message">
        <div class="logout-confirmation-content">
            <p class="logout-confirmation-message" id="admin-logout-confirmation-message">apakah anda yakin untuk log out?</p>
            <div class="logout-confirmation-actions">
                <button class="logout-confirmation-button logout-confirmation-cancel" type="button" data-logout-confirmation-cancel>Batal</button>
                <button class="logout-confirmation-button logout-confirmation-submit" type="submit">Log out</button>
            </div>
        </div>
    </dialog>
</form>
<script>
    (() => {
        const dialog = document.getElementById('admin-logout-confirmation');
        const openButton = document.querySelector('[data-logout-confirmation-open]');
        const cancelButton = dialog.querySelector('[data-logout-confirmation-cancel]');

        openButton.addEventListener('click', () => dialog.showModal());
        cancelButton.addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    })();
</script>
