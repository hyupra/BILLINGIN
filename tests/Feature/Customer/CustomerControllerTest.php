<?php

use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use App\Modules\Platform\Models\Tenant;
use App\Modules\Identity\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(fn () => $this->withoutMiddleware(PreventRequestForgery::class));

test('a logged-in admin can create a customer', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    $package = Package::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'package_id' => $package->id,
        'billing_type' => 'postpaid',
    ]);

    $response->assertRedirect(route('customers.index'));
    expect(Customer::where('name', 'Budi Santoso')->where('tenant_id', $tenant->id)->exists())->toBeTrue();
});

test('creating a customer without required fields fails validation and nothing is saved', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('customers.store'), []);

    $response->assertSessionHasErrors(['name', 'phone', 'package_id', 'billing_type']);
    expect(Customer::count())->toBe(0);
});

test('an invalid phone format is rejected', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    $package = Package::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'name' => 'Budi Santoso',
        'phone' => '12345',
        'package_id' => $package->id,
        'billing_type' => 'postpaid',
    ]);

    $response->assertSessionHasErrors('phone');
});

test('the same ppp_username is allowed on two different routers, rejected twice on the same router', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for these direct Eloquent creates
    $package = Package::factory()->create(['tenant_id' => $tenant->id]);
    $routerA = Router::factory()->create(['tenant_id' => $tenant->id]);
    $routerB = Router::factory()->create(['tenant_id' => $tenant->id]);

    $payload = [
        'name' => 'Pelanggan Satu',
        'phone' => '081234567890',
        'package_id' => $package->id,
        'billing_type' => 'postpaid',
        'ppp_username' => 'budi-01',
    ];

    $this->actingAs($user)->post(route('customers.store'), [...$payload, 'router_id' => $routerA->id])
        ->assertRedirect(route('customers.index'));

    // same username, different router: allowed
    $this->actingAs($user)->post(route('customers.store'), [
        ...$payload, 'name' => 'Pelanggan Dua', 'router_id' => $routerB->id,
    ])->assertRedirect(route('customers.index'));

    // same username, same router as the first: rejected
    $response = $this->actingAs($user)->post(route('customers.store'), [
        ...$payload, 'name' => 'Pelanggan Tiga', 'router_id' => $routerA->id,
    ]);
    $response->assertSessionHasErrors('ppp_username');

    expect(Customer::where('ppp_username', 'budi-01')->count())->toBe(2);
});

test('tenant B gets a 404 editing, updating, or deleting tenant A customer by ID', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->create(['tenant_id' => $tenantB->id]);

    $this->actingAs(User::factory()->create(['tenant_id' => $tenantA->id]));
    TenantContext::apply($tenantA->id, false); // RLS block predicate needs SESSION_CONTEXT set for these direct Eloquent creates
    $packageA = Package::factory()->create(['tenant_id' => $tenantA->id]);
    $customerA = Customer::factory()->create(['tenant_id' => $tenantA->id, 'package_id' => $packageA->id]);

    $this->actingAs($userB)->get(route('customers.edit', $customerA))->assertNotFound();
    $this->actingAs($userB)->put(route('customers.update', $customerA), ['name' => 'Hacked'])->assertNotFound();
    $this->actingAs($userB)->delete(route('customers.destroy', $customerA))->assertNotFound();

    // These checks care about the row's physical DB state, not visibility.
    // Bypassing the Eloquent scope alone isn't enough: SQL Server's RLS
    // FILTER PREDICATE is a separate, DB-layer check against
    // SESSION_CONTEXT('tenant_id'), still set to tenant B from the last
    // HTTP call above — switch to the superadmin session context (same
    // pattern as tests/Feature/Tenancy/TenantIsolationRlsTest.php) to
    // bypass both layers at once.
    TenantContext::apply(null, true);
    $untouched = Customer::find($customerA->id);
    expect($untouched)->not->toBeNull();
    expect($untouched->name)->not->toBe('Hacked');
});

test('tenant B cannot reference tenant A package_id or router_id when creating a customer', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->create(['tenant_id' => $tenantB->id]);

    $this->actingAs(User::factory()->create(['tenant_id' => $tenantA->id]));
    TenantContext::apply($tenantA->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    $packageA = Package::factory()->create(['tenant_id' => $tenantA->id]);

    $response = $this->actingAs($userB)->post(route('customers.store'), [
        'name' => 'Pelanggan B',
        'phone' => '081234567890',
        'package_id' => $packageA->id, // belongs to tenant A
        'billing_type' => 'postpaid',
    ]);

    $response->assertSessionHasErrors('package_id');
    expect(Customer::count())->toBe(0);
});
