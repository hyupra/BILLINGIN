<?php

use App\Modules\Master\Models\Router;
use App\Modules\Platform\Models\Tenant;
use App\Support\Tenancy\TenantContext;

test('a router can be created and its API credentials are encrypted at rest', function () {
    $tenant = Tenant::factory()->create();
    TenantContext::apply($tenant->id, false);

    $router = Router::create([
        'name' => 'MT-Test-01',
        'host' => '192.168.88.1',
        'api_port' => 8728,
        'api_username_enc' => 'admin',
        'api_password_enc' => 'secret123',
    ]);

    expect($router->tenant_id)->toBe($tenant->id);
    expect($router->fresh()->api_password_enc)->toBe('secret123'); // decrypts transparently

    $raw = \DB::table('routers')->where('id', $router->id)->value('api_password_enc');
    expect($raw)->not->toBe('secret123'); // stored ciphertext, not plaintext
});

test('api credentials never appear in array/json output', function () {
    $tenant = Tenant::factory()->create();
    TenantContext::apply($tenant->id, false);

    $router = Router::create([
        'name' => 'MT-Test-02',
        'host' => '192.168.88.1',
        'api_username_enc' => 'admin',
        'api_password_enc' => 'secret123',
    ]);

    expect($router->toArray())->not->toHaveKey('api_username_enc');
    expect($router->toArray())->not->toHaveKey('api_password_enc');
});
