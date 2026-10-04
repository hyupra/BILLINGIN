<?php

use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Platform\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantScope;

// Same defense-in-depth proof as TenantIsolationRlsTest: the Eloquent scope
// is deliberately bypassed, so this only passes if SQL Server's RLS policy
// itself is blocking the row.

test('tenant B cannot read tenant A customers even with the Eloquent scope bypassed', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    TenantContext::apply($tenantA->id, false);
    $package = Package::factory()->create(['tenant_id' => $tenantA->id]);
    $customerA = Customer::factory()->create(['tenant_id' => $tenantA->id, 'package_id' => $package->id]);

    TenantContext::apply($tenantB->id, false);
    $visible = Customer::withoutGlobalScope(TenantScope::class)->get();

    expect($visible->pluck('id'))->not->toContain($customerA->id);
});
