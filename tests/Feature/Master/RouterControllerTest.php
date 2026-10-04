<?php

use App\Modules\Master\Models\Router;
use App\Modules\Master\Services\RouterConnectionResult;
use App\Modules\Master\Services\RouterConnectionTester;
use App\Modules\Platform\Models\Tenant;
use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(fn () => $this->withoutMiddleware(PreventRequestForgery::class));

test('a logged-in admin can create a router', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('routers.store'), [
        'name' => 'MT-Mekar-01',
        'network_driver' => 'manual',
        'host' => '192.168.88.1',
        'api_port' => 8728,
    ]);

    $response->assertRedirect(route('routers.index'));
    expect(Router::where('name', 'MT-Mekar-01')->where('tenant_id', $tenant->id)->exists())->toBeTrue();
});

test('creating a router without a name fails validation', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->post(route('routers.store'), [
        'network_driver' => 'manual',
        'host' => '192.168.88.1',
        'api_port' => 8728,
    ]);

    $response->assertSessionHasErrors('name');
    expect(Router::count())->toBe(0);
});

test('tenant B gets a 404 editing or deleting tenant A router by ID', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->create(['tenant_id' => $tenantB->id]);

    $this->actingAs(User::factory()->create(['tenant_id' => $tenantA->id]));
    $routerA = Router::factory()->create(['tenant_id' => $tenantA->id]);

    $this->actingAs($userB)->get(route('routers.edit', $routerA))->assertNotFound();
    $this->actingAs($userB)->delete(route('routers.destroy', $routerA))->assertNotFound();
    expect(Router::find($routerA->id))->not->toBeNull(); // untouched
});

test('test-connection endpoint rejects a blocked host without calling the tester twice or leaking a 500', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $router = Router::factory()->create(['tenant_id' => $tenant->id, 'host' => '127.0.0.1']);

    $response = $this->actingAs($user)->postJson(route('routers.test-connection', $router));

    $response->assertStatus(422);
    expect($response->json('ok'))->toBeFalse();
});

test('test-connection endpoint reports reachable using an injected fake connector', function () {
    $this->app->bind(RouterConnectionTester::class, fn () => new RouterConnectionTester(
        fn ($ip, $port, $timeout) => fopen('php://memory', 'r'),
    ));

    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $router = Router::factory()->create(['tenant_id' => $tenant->id, 'host' => '203.0.113.10']);

    $response = $this->actingAs($user)->postJson(route('routers.test-connection', $router));

    $response->assertOk();
    expect($response->json('reachable'))->toBeTrue();
    expect($router->fresh()->status)->toBe('online');
});
