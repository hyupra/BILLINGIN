<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ponytail: Fortify redirects here after login (config/fortify.php 'home').
// No real dashboard exists until S6 — send authenticated users to the
// closest thing we have (the static mockup) instead of a 404. Replace with
// a real dashboard controller/route once S6 builds it.
Route::get('/home', function () {
    return redirect('/mockup/dashboard/overview');
})->middleware('auth')->name('home');

/*
|--------------------------------------------------------------------------
| Static design mockups (no backend wiring)
|--------------------------------------------------------------------------
|
| Visual-only screens built from docs/design/png/ ahead of the sprints
| (S1/S2/S6/Frontend) that would normally power them with real data. Kept
| under /mockup/* specifically so they're never confused with real routes
| once those sprints build the actual pages at their real URLs.
*/
Route::get('/mockup', function () {
    return view('mockup.index', ['groups' => [
        'Landing & Auth' => [
            'Landing page' => '/mockup/landing',
            'Daftar akun' => '/mockup/register',
            'Lupa password' => '/mockup/forgot-password',
            'Reset password' => '/mockup/reset-password',
        ],
        'Pembayaran pelanggan' => [
            'Cek tagihan' => '/mockup/pay/check-bill',
            'Detail tagihan' => '/mockup/pay/bill-detail',
            'Pilih metode bayar' => '/mockup/pay/payment-method',
            'Instruksi QRIS' => '/mockup/pay/qris-instructions',
            'Instruksi Virtual Account' => '/mockup/pay/va-instructions',
            'Pembayaran berhasil' => '/mockup/pay/payment-success',
            'Halaman isolir' => '/mockup/pay/suspended',
            'Pendaftaran baru' => '/mockup/pay/register',
            'Bantuan & FAQ' => '/mockup/pay/help',
        ],
        'Dashboard mitra' => [
            'Ringkasan' => '/mockup/dashboard/overview',
            'Pelanggan' => '/mockup/dashboard/customers',
            'Tambah pelanggan' => '/mockup/dashboard/customers-create',
            'Impor pelanggan CSV' => '/mockup/dashboard/customers-import',
            'Tagihan' => '/mockup/dashboard/invoices',
            'Pembayaran' => '/mockup/dashboard/payments',
            'Paket' => '/mockup/dashboard/packages',
            'Router' => '/mockup/dashboard/routers',
            'Laporan' => '/mockup/dashboard/reports',
            'Pengaturan' => '/mockup/dashboard/settings',
            'Langganan' => '/mockup/dashboard/subscription',
        ],
    ]]);
});

Route::get('/mockup/{path}', function (string $path) {
    return view('mockup.'.str_replace('/', '.', $path));
})->where('path', '.*');
