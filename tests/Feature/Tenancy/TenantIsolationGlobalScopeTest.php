<?php

use App\Modules\Identity\Models\AuditLog;
use App\Modules\Platform\Models\Tenant;
use App\Support\Tenancy\TenantContext;

// TenantContext::apply() is called directly rather than going through a real
// HTTP request: it's exactly what SetTenantContext middleware does, and
// testing it directly keeps this test from being coupled to which routes
// happen to sit behind the `web` middleware group.

test('tenant B cannot read tenant A audit logs through the Eloquent global scope', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    TenantContext::apply($tenantA->id, false);
    $logA = AuditLog::factory()->create();

    TenantContext::apply($tenantB->id, false);
    $visible = AuditLog::all();

    expect($visible->pluck('id'))->not->toContain($logA->id);
});

test('superadmin bypasses the tenant scope and sees logs from every tenant', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    TenantContext::apply($tenantA->id, false);
    $logA = AuditLog::factory()->create();

    TenantContext::apply($tenantB->id, false);
    $logB = AuditLog::factory()->create();

    TenantContext::apply(null, true);
    $visible = AuditLog::all()->pluck('id');

    expect($visible)->toContain($logA->id)->toContain($logB->id);
});
