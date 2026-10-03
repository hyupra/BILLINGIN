<?php

use App\Modules\Identity\Models\AuditLog;
use App\Modules\Platform\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantScope;

// Defense-in-depth proof: the Eloquent scope is deliberately bypassed here,
// so this only passes if SQL Server's row-level security policy itself
// (sec.tenant_policy, migration 2026_10_03_000005) is blocking the row, not
// the application-layer scope tested in TenantIsolationGlobalScopeTest.

test('tenant B cannot read tenant A audit logs even with the Eloquent scope bypassed', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    TenantContext::apply($tenantA->id, false);
    $logA = AuditLog::factory()->create();

    TenantContext::apply($tenantB->id, false);
    $visible = AuditLog::withoutGlobalScope(TenantScope::class)->get();

    expect($visible->pluck('id'))->not->toContain($logA->id);
});

test('superadmin session context bypasses RLS even with the Eloquent scope bypassed', function () {
    $tenantA = Tenant::factory()->create();

    TenantContext::apply($tenantA->id, false);
    $logA = AuditLog::factory()->create();

    TenantContext::apply(null, true);
    $visible = AuditLog::withoutGlobalScope(TenantScope::class)->get();

    expect($visible->pluck('id'))->toContain($logA->id);
});
