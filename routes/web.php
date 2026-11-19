
<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminPasswordController;
use App\Http\Controllers\Admin\DataPetugasController;
use App\Http\Controllers\Admin\DataWargaController;
use App\Http\Controllers\Admin\ProfileAdminController;
use App\Http\Controllers\Admin\TransaksiSampahController;
use App\Http\Controllers\User\FinanceController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [AdminLoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.process');

    Route::get('/lupa-password', [AdminPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('/lupa-password', [AdminPasswordController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::post('/lupa-password/verifikasi', [AdminPasswordController::class, 'updateWithCurrentPassword'])
        ->middleware('throttle:5,1')
        ->name('password.change');

    Route::get('/reset-password/{token}', [AdminPasswordController::class, 'edit'])
        ->name('password.reset');

    Route::post('/reset-password', [AdminPasswordController::class, 'update'])
        ->middleware('throttle:5,1')
        ->name('password.update');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)
            ->name('dashboard');
        Route::get('/profile', [ProfileAdminController::class, 'show'])
            ->name('profile.show');
        Route::get('/profile/photo', [ProfileAdminController::class, 'photo'])
            ->name('profile.photo');
        Route::put('/profile', [ProfileAdminController::class, 'update'])
            ->name('profile.update');
        Route::put('/profile/password', [ProfileAdminController::class, 'updatePassword'])
            ->name('profile.password');

        Route::get('/data-warga', [DataWargaController::class, 'index'])
            ->name('warga.index');
        Route::get('/data-warga/create', [DataWargaController::class, 'create'])
            ->name('warga.create');
        Route::post('/data-warga', [DataWargaController::class, 'store'])
            ->name('warga.store');
        Route::get('/data-warga/laporan.pdf', [DataWargaController::class, 'exportPdf'])
            ->name('warga.pdf');
        Route::get('/data-warga/{warga}/edit', [DataWargaController::class, 'edit'])
            ->whereNumber('warga')
            ->name('warga.edit');
        Route::put('/data-warga/{warga}', [DataWargaController::class, 'update'])
            ->whereNumber('warga')
            ->name('warga.update');
        Route::delete('/data-warga/{warga}', [DataWargaController::class, 'destroy'])
            ->whereNumber('warga')
            ->name('warga.destroy');

        Route::get('/data-petugas', [DataPetugasController::class, 'index'])
            ->name('petugas.index');
        Route::get('/data-petugas/create', [DataPetugasController::class, 'create'])
            ->name('petugas.create');
        Route::post('/data-petugas', [DataPetugasController::class, 'store'])
            ->name('petugas.store');
        Route::get('/data-petugas/laporan.pdf', [DataPetugasController::class, 'exportPdf'])
            ->name('petugas.pdf');
        Route::get('/data-petugas/{petugas}/edit', [DataPetugasController::class, 'edit'])
            ->whereNumber('petugas')
            ->name('petugas.edit');
        Route::put('/data-petugas/{petugas}', [DataPetugasController::class, 'update'])
            ->whereNumber('petugas')
            ->name('petugas.update');
        Route::delete('/data-petugas/{petugas}', [DataPetugasController::class, 'destroy'])
            ->whereNumber('petugas')
            ->name('petugas.destroy');

        Route::get('/transaksi-sampah', [TransaksiSampahController::class, 'index'])
            ->name('transaksi.index');
        Route::get('/transaksi-sampah/create', [TransaksiSampahController::class, 'create'])
            ->name('transaksi.create');
        Route::post('/transaksi-sampah', [TransaksiSampahController::class, 'store'])
            ->name('transaksi.store');
        Route::get('/transaksi-sampah/laporan.pdf', [TransaksiSampahController::class, 'exportPdf'])
            ->name('transaksi.pdf');
        Route::get('/transaksi-sampah/{transaction}/edit', [TransaksiSampahController::class, 'edit'])
            ->whereNumber('transaction')
            ->name('transaksi.edit');
        Route::put('/transaksi-sampah/{transaction}', [TransaksiSampahController::class, 'update'])
            ->whereNumber('transaction')
            ->name('transaksi.update');
        Route::delete('/transaksi-sampah/{transaction}', [TransaksiSampahController::class, 'destroy'])
            ->whereNumber('transaction')
            ->name('transaksi.destroy');
        Route::get('/transaksi-sampah/{transaction}/bayar', [TransaksiSampahController::class, 'showPayment'])
            ->whereNumber('transaction')
            ->name('transaksi.payment');
        Route::post('/transaksi-sampah/{transaction}/bayar', [TransaksiSampahController::class, 'pay'])
            ->whereNumber('transaction')
            ->name('transaksi.pay');

        Route::post('/logout', [AdminLoginController::class, 'destroy'])
            ->name('logout');
    });
});

Route::get('/login', [LoginController::class, 'create'])
    ->name('login');

Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('login.store');

Route::middleware('auth')->group(function () {
    Route::get('/beranda', [LoginController::class, 'dashboard'])
        ->name('user.dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('user.profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('user.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('user.profile.update');
    Route::get('/pengaturan-akun', [ProfileController::class, 'settings'])
        ->name('user.settings');
    Route::get('/bantuan', [ProfileController::class, 'help'])
        ->name('user.help');
    Route::get('/riwayat', [HistoryController::class, 'index'])
        ->name('user.riwayat');
    Route::get('/keuangan', [FinanceController::class, 'index'])
        ->name('user.keuangan');
    Route::get('/keuangan/pencairan', [FinanceController::class, 'createWithdrawal'])
        ->name('user.keuangan.withdrawals.create');
    Route::post('/keuangan/pencairan', [FinanceController::class, 'storeWithdrawal'])
        ->name('user.keuangan.withdrawals.store');
    Route::get('/notifikasi', [NotificationController::class, 'index'])
        ->name('user.notifications.index');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllRead'])
        ->name('user.notifications.read-all');
    Route::get('/notifikasi/{notification}', [NotificationController::class, 'show'])
        ->whereNumber('notification')
        ->name('user.notifications.show');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');
});

Route::get('/register', [RegisterController::class, 'create'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('register.store');

Route::get('/lupa-password', [PasswordResetController::class, 'create'])
    ->name('password.request');

Route::post('/lupa-password', [PasswordResetController::class, 'emailResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'update'])
    ->middleware('throttle:5,1')
    ->name('password.update');
