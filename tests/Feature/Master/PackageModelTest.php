<?php

use App\Modules\Master\Models\Package;
use App\Modules\Platform\Models\Tenant;
use App\Support\Tenancy\TenantContext;

test('total price is computed from base price and ppn, not stored', function () {
    $tenant = Tenant::factory()->create();
    TenantContext::apply($tenant->id, false);

    $package = Package::create([
        'name' => 'Home 20 Mbps',
        'speed_label' => '20 Mbps',
        'base_price' => 150000,
        'ppn_percent' => 11,
    ]);

    expect($package->totalPrice())->toBe(166500);
});
