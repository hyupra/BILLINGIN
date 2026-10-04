<?php

namespace App\Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customer\Http\Requests\StoreCustomerRequest;
use App\Modules\Customer\Http\Requests\UpdateCustomerRequest;
use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['package', 'router'])->orderBy('name')->get();

        return view('mockup.dashboard.customers', ['customers' => $customers]);
    }

    public function create()
    {
        return view('mockup.dashboard.customers-create', [
            'packages' => Package::where('is_active', true)->orderBy('name')->get(),
            'routers' => Router::orderBy('name')->get(),
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['id_number']);
        $data['id_number_enc'] = $request->input('id_number');
        $data['due_day'] ??= 10; // S1 hardcoded fallback — see spec's "customer_code" note on tenants.due_day_default not being wired up here yet

        $attempts = 0;
        while (true) {
            $data['customer_code'] = $this->nextCustomerCode();
            try {
                Customer::create($data);
                break;
            } catch (QueryException $e) {
                if ($e->getCode() !== '23000' || ++$attempts >= 3) {
                    throw $e;
                }
            }
        }

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('mockup.dashboard.customers-edit', [
            'customer' => $customer,
            'packages' => Package::where('is_active', true)->orderBy('name')->get(),
            'routers' => Router::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->safe()->except(['id_number']);

        if ($request->filled('id_number')) {
            $data['id_number_enc'] = $request->input('id_number');
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Pelanggan dihapus.');
    }

    /**
     * Sequential per tenant, formatted C-1000, C-1001, ... The unique DB
     * index (uq_customers_code, Task 3 migration) is the real guard; a
     * collision here just retries (caught in store() above) since this is
     * a low-traffic admin form, not a high-concurrency path.
     */
    private function nextCustomerCode(): string
    {
        return 'C-'.(1000 + Customer::withTrashed()->count());
    }
}
