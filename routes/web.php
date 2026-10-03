<?php

use Illuminate\Support\Facades\Route;

// ponytail: redirect to the landing mockup instead of Laravel's default
// welcome page now that one exists (QA v2 C-04). Swap for a real
// marketing-controller route once the Frontend sprint builds one.
Route::get('/', function () {
    return redirect('/mockup/landing');
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

// QA v2 S-01: the partner dashboard represents an authenticated area and
// was reachable with zero login (same static mockup data for anyone who
// found the URL). Registered before the general /mockup/{path} catch-all
// below so it takes priority for anything under dashboard/.
Route::get('/mockup/dashboard/{path}', function (string $path) {
    $view = 'mockup.dashboard.'.str_replace('/', '.', $path);
    abort_unless(view()->exists($view), 404);

    return view($view);
})->middleware('auth')->where('path', '.*');

// QA v2 C-02: an unknown path previously hit view()'s own
// InvalidArgumentException, rendering as a 500 with a full stack trace
// (file paths, framework/PHP versions) instead of a clean 404.
Route::get('/mockup/{path}', function (string $path) {
    $view = 'mockup.'.str_replace('/', '.', $path);
    abort_unless(view()->exists($view), 404);

    return view($view);
})->where('path', '.*');
