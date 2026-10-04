<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Master\Http\Requests\StorePackageRequest;
use App\Modules\Master\Http\Requests\UpdatePackageRequest;
use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('customers')->with('router')->orderBy('name')->get();

        return view('mockup.dashboard.packages', ['packages' => $packages]);
    }

    public function create()
    {
        return view('mockup.dashboard.packages-create', ['routers' => Router::orderBy('name')->get()]);
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        Package::create($request->validated());

        return redirect()->route('packages.index')->with('status', 'Paket berhasil ditambahkan.');
    }

    public function edit(Package $package)
    {
        return view('mockup.dashboard.packages-edit', [
            'package' => $package,
            'routers' => Router::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $package->update($request->validated());

        return redirect()->route('packages.index')->with('status', 'Paket berhasil diperbarui.');
    }

    /**
     * Never a hard delete: customers.package_id is a required, live FK
     * (unlike invoice_items in the full spec, which snapshots price at
     * invoice time) — removing the row would orphan every customer still
     * on this package. "Hapus" in the UI deactivates instead; inactive
     * packages drop out of the Tambah/Ubah Pelanggan dropdowns but existing
     * customers referencing them are unaffected.
     */
    public function destroy(Package $package): RedirectResponse
    {
        $package->update(['is_active' => false]);

        return redirect()->route('packages.index')->with('status', 'Paket dinonaktifkan.');
    }
}
