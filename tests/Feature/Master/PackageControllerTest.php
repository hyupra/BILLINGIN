<?php

use App\Modules\Master\Models\Package;
use App\Modules\Customer\Models\Customer;
use App\Modules\Platform\Models\Tenant;
use App\Modules\Identity\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(fn () => $this->withoutMiddleware(PreventRequestForgery::class));

test('a logged-in admin can create a package', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('packages.store'), [
        'name' => 'Home 20 Mbps',
        'speed_label' => '20 Mbps',
        'base_price' => 150000,
        'ppn_percent' => 11,
    ]);

    $response->assertRedirect(route('packages.index'));
    expect(Package::where('name', 'Home 20 Mbps')->where('tenant_id', $tenant->id)->exists())->toBeTrue();
});

test('creating a package without required fields fails validation', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('packages.store'), []);

    $response->assertSessionHasErrors(['name', 'speed_label', 'base_price', 'ppn_percent']);
});

test('deleting a package deactivates it instead of removing the row, and existing customers keep it', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for these direct Eloquent creates
    $package = Package::factory()->create(['tenant_id' => $tenant->id, 'is_active' => true]);
    $customer = Customer::factory()->create(['tenant_id' => $tenant->id, 'package_id' => $package->id]);

    $this->actingAs($user)->delete(route('packages.destroy', $package));

    expect($package->fresh()->is_active)->toBeFalse();
    expect(Package::find($package->id))->not->toBeNull(); // row still exists
    expect($customer->fresh()->package_id)->toBe($package->id); // customer unaffected
});

test('tenant B gets a 404 editing tenant A package by ID', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->create(['tenant_id' => $tenantB->id]);

    $this->actingAs(User::factory()->create(['tenant_id' => $tenantA->id]));
    TenantContext::apply($tenantA->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    $packageA = Package::factory()->create(['tenant_id' => $tenantA->id]);

    $this->actingAs($userB)->get(route('packages.edit', $packageA))->assertNotFound();
});
