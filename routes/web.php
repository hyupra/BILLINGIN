<?php

use App\Modules\Customer\Http\Controllers\CustomerController;
use App\Modules\Customer\Http\Controllers\CustomerImportController;
use App\Modules\Master\Http\Controllers\PackageController;
use App\Modules\Master\Http\Controllers\RouterController;
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

Route::middleware('auth')->group(function () {
    Route::get('/mockup/dashboard/routers', [RouterController::class, 'index'])->name('routers.index');
    Route::get('/mockup/dashboard/routers-create', [RouterController::class, 'create'])->name('routers.create');
    Route::post('/mockup/dashboard/routers', [RouterController::class, 'store'])->name('routers.store');
    Route::get('/mockup/dashboard/routers/{router}/edit', [RouterController::class, 'edit'])->name('routers.edit');
    Route::put('/mockup/dashboard/routers/{router}', [RouterController::class, 'update'])->name('routers.update');
    Route::delete('/mockup/dashboard/routers/{router}', [RouterController::class, 'destroy'])->name('routers.destroy');
    Route::post('/mockup/dashboard/routers/{router}/test-connection', [RouterController::class, 'testConnection'])
        ->middleware('throttle:10,1')->name('routers.test-connection');

    Route::get('/mockup/dashboard/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/mockup/dashboard/packages-create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/mockup/dashboard/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/mockup/dashboard/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/mockup/dashboard/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/mockup/dashboard/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('/mockup/dashboard/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/mockup/dashboard/customers-create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/mockup/dashboard/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/mockup/dashboard/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/mockup/dashboard/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/mockup/dashboard/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/mockup/dashboard/customers-import', [CustomerImportController::class, 'create'])->name('customers.import.create');
    Route::post('/mockup/dashboard/customers-import', [CustomerImportController::class, 'store'])->name('customers.import.store');
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
