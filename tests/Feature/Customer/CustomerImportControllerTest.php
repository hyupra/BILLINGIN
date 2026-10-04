<?php

use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use App\Modules\Platform\Models\Tenant;
use App\Modules\Identity\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;

beforeEach(fn () => $this->withoutMiddleware(PreventRequestForgery::class));

function makeCsv(string $content): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'csv');
    file_put_contents($path, $content);

    return new UploadedFile($path, 'import.csv', 'text/csv', null, true);
}

test('valid rows are imported and invalid rows are reported without aborting the batch', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    Package::factory()->create(['tenant_id' => $tenant->id, 'name' => 'Home 20 Mbps', 'is_active' => true]);

    $csv = "nama,wa,alamat,paket,username_pppoe\n"
        ."Budi Santoso,081234567890,Jl. Mekar,Home 20 Mbps,budi-01\n"
        .",081234567891,Jl. Mekar,Home 20 Mbps,kosong-nama\n" // invalid: empty nama
        ."Siti Rahma,bukan-nomor,Jl. Mekar,Home 20 Mbps,siti-01\n" // invalid: bad phone
        ."Agus Wijaya,081234567892,Jl. Mekar,Paket Tidak Ada,agus-01\n" // invalid: unknown package
        ."Dewi Lestari,081234567893,Jl. Mekar,Home 20 Mbps,dewi-01\n";

    $response = $this->actingAs($user)->post(route('customers.import.store'), [
        'csv_file' => makeCsv($csv),
    ]);

    $response->assertSessionHas('import_summary');
    $summary = session('import_summary');
    expect($summary['created'])->toBe(2); // Budi + Dewi
    expect($summary['errors'])->toHaveCount(3);
    expect(Customer::where('tenant_id', $tenant->id)->count())->toBe(2);
});

test('a CSV missing a required header column is rejected with one clear message, no rows processed', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    Package::factory()->create(['tenant_id' => $tenant->id, 'name' => 'Home 20 Mbps']);

    $csv = "nama,wa,alamat,username_pppoe\n" // missing "paket"
        ."Budi Santoso,081234567890,Jl. Mekar,budi-01\n";

    $response = $this->actingAs($user)->post(route('customers.import.store'), [
        'csv_file' => makeCsv($csv),
    ]);

    $response->assertSessionHasErrors('csv_file');
    expect(Customer::where('tenant_id', $tenant->id)->count())->toBe(0);
});

test('a duplicate ppp_username partway through the file rolls back the whole batch with a clear message, not a 500', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for these direct Eloquent creates
    Package::factory()->create(['tenant_id' => $tenant->id, 'name' => 'Home 20 Mbps', 'is_active' => true]);
    $router = Router::factory()->create(['tenant_id' => $tenant->id]);
    Customer::factory()->create([
        'tenant_id' => $tenant->id,
        'router_id' => $router->id,
        'ppp_username' => 'dup-01',
    ]);

    // CSV import doesn't assign router_id (S1 keeps import simple — see
    // controller), so this specific duplicate path is exercised at the
    // model layer directly instead of faking a router column the import
    // format doesn't have yet:
    $csv = "nama,wa,alamat,paket,username_pppoe\n"
        ."Valid Satu,081234567890,Jl. Mekar,Home 20 Mbps,ok-01\n"
        ."Valid Dua,081234567891,Jl. Mekar,Home 20 Mbps,ok-01\n"; // duplicate within the same file, no router_id -> tenant-wide-null-router collision

    $response = $this->actingAs($user)->post(route('customers.import.store'), [
        'csv_file' => makeCsv($csv),
    ]);

    $response->assertSessionHasErrors('csv_file');
    expect(Customer::where('ppp_username', 'ok-01')->count())->toBe(0); // whole batch rolled back
});
