<style>
    .logout-confirmation {
        width: min(400px, calc(100vw - 32px));
        max-width: none;
        margin: auto;
        padding: 0;
        border: 0;
        border-radius: 14px;
        color: #17231d;
        background: #fff;
        box-shadow: 0 18px 55px rgb(0 0 0 / 24%);
    }

    .logout-confirmation::backdrop {
        background: rgb(17 33 24 / 52%);
        backdrop-filter: blur(2px);
    }

    .logout-confirmation[open] {
        animation: logout-confirmation-enter 200ms ease-out both;
    }

    .logout-confirmation-content {
        padding: 24px;
    }

    .logout-confirmation-message {
        margin: 0 0 22px;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
    }

    .logout-confirmation-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .logout-confirmation-button {
        min-height: 40px;
        padding: 8px 18px;
        border: 1px solid transparent;
        border-radius: 8px;
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 160ms ease, background-color 160ms ease;
    }

    .logout-confirmation-button:hover {
        transform: translateY(-1px);
    }

    .logout-confirmation-button:active {
        transform: scale(0.98);
    }

    .logout-confirmation-cancel {
        border-color: #d7e1dc;
        background: #fff;
        color: #415349;
    }

    .logout-confirmation-submit {
        background: #F0442D;
        color: #fff;
    }

    .logout-confirmation-submit:hover {
        background: #D93625;
    }

    .logout-confirmation-button:focus-visible {
        outline-offset: 2px;
    }

    .logout-confirmation-submit:focus-visible {
        outline: 3px solid rgb(240 68 45 / 32%);
    }

    .logout-confirmation-cancel:focus-visible {
        outline: 3px solid rgb(65 83 73 / 24%);
    }

    @keyframes logout-confirmation-enter {
        from {
            opacity: 0;
            transform: translateY(8px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .logout-confirmation[open] {
            animation-duration: 0.01ms;
        }

        .logout-confirmation-button {
            transition-duration: 0.01ms;
        }
    }
</style>
