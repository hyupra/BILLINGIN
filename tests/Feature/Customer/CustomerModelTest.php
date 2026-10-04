<?php

use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Platform\Models\Tenant;
use App\Support\Tenancy\TenantContext;

test('a customer belongs to a package and its NIK is encrypted at rest', function () {
    $tenant = Tenant::factory()->create();
    TenantContext::apply($tenant->id, false);
    $package = Package::factory()->create(['tenant_id' => $tenant->id]);

    $customer = Customer::create([
        'customer_code' => 'C-1000',
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'id_number_enc' => '3201012345670001',
        'package_id' => $package->id,
        'billing_type' => 'postpaid',
        'status' => 'active',
    ]);

    expect($customer->package->id)->toBe($package->id);
    expect($customer->fresh()->id_number_enc)->toBe('3201012345670001');

    $raw = \DB::table('customers')->where('id', $customer->id)->value('id_number_enc');
    expect($raw)->not->toBe('3201012345670001');
});

test('soft-deleted customers are excluded from default queries', function () {
    $tenant = Tenant::factory()->create();
    TenantContext::apply($tenant->id, false);
    $package = Package::factory()->create(['tenant_id' => $tenant->id]);
    $customer = Customer::factory()->create(['tenant_id' => $tenant->id, 'package_id' => $package->id]);

    $customer->delete();

    expect(Customer::find($customer->id))->toBeNull();
    expect(Customer::withTrashed()->find($customer->id))->not->toBeNull();
});
