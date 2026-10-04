# S1 — Master Data (Paket, Router) & Pelanggan Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the static Blade mockup data for Pelanggan, Paket, and Router with real database-backed CRUD, under the existing `/mockup/dashboard/*` URLs, with tenant isolation enforced at both the Eloquent and SQL Server RLS layers.

**Architecture:** Two new modules (`App\Modules\Master` for Package/Router, `App\Modules\Customer` for Customer) following the existing `Models/` + `Http/Controllers/` layout already used by `App\Modules\Identity` and `App\Modules\Platform`. Plain Eloquent relations between modules (no Service/Event layer yet — see note below). Routes stay under `/mockup/dashboard/*`, registered before the generic catch-all closure in `routes/web.php`.

**Tech Stack:** Laravel 13.17, PHP 8.3+, SQL Server (sqlsrv), Pest (DatabaseTransactions, no RefreshDatabase — see `tests/Pest.php`), Blade + Tailwind CDN (no build step).

**Spec:** `docs/superpowers/specs/2026-10-04-s1-master-data-customer-design.md` (and the full roadmap at `docs/spec/BACKEND-BILLINGIN-laravel-sqlserver.md`, §5/§13 for the un-trimmed reference schema/routes this plan deliberately simplifies).

**Module-boundary note:** the full backend spec (`docs/spec/...` §3.2) says modules should only talk via Service/Action/Event, not direct Eloquent queries across module tables. This plan uses plain `hasMany`/`belongsTo` relations across `Master`↔`Customer` (e.g. `Package::customers()`) instead, because no second consumer exists yet to justify a Service layer, and the relations mirror the ERD's own foreign keys. Introduce the Service/Event layer when a sprint (likely S2, Billing reacting to Customer events) actually needs the indirection — don't build it speculatively now.

## Global Constraints

- PHP `^8.3`, Laravel `^13.17` (from `composer.json`) — don't introduce syntax or packages needing newer versions.
- DB is SQL Server: money as `bigint` (never float/decimal for rupiah amounts other than `ppn_percent`), enum-like columns as `string` + `CHECK` constraint via `DB::statement(...)` (SQL Server has no native enum) — follow the exact pattern in `database/migrations/2026_10_03_000001_create_tenants_table.php`.
- All new models use `App\Support\Tenancy\BelongsToTenant` and the `#[Fillable([...])]` / `casts(): array` attribute style already used by `App\Modules\Identity\Models\User` and `App\Modules\Platform\Models\Tenant` — not `protected $fillable` / `protected $casts`.
- Encrypted columns (`*_enc`) use Eloquent's native `'encrypted'` cast, stored as `text()` (not `string()`/255) to avoid ciphertext truncation, and are listed in `#[Hidden([...])]` so they never serialize into views/JSON.
- Every new route lives under the `auth` middleware group and at a `/mockup/dashboard/*` path — never `/admin/*` (see spec §"Keputusan desain" point 4) — and must be registered in `routes/web.php` **above** the existing `Route::get('/mockup/dashboard/{path}', ...)` catch-all (currently the block starting with the `// QA v2 S-01` comment), or Laravel's catch-all swallows the request first.
- `Rule::exists(...)` / `Rule::unique(...)` validation against other tenant-scoped tables (`routers`, `packages`) must explicitly `->where('tenant_id', TenantContext::tenantId())` — the plain `exists:table,column` rule bypasses Eloquent's global scope and would let a malicious tenant reference another tenant's row by ID.
- Tests: `tests/Pest.php` binds `Feature` to `DatabaseTransactions` (not `RefreshDatabase` — SQL Server RLS blocks `DROP TABLE`). New migrations must be run once against the dev/test DB (`php artisan migrate`) before running Pest; each test wraps in a rolled-back transaction.
- Package deletion is a soft toggle (`is_active = false`), never a hard `DELETE` — `customers.package_id` is a live FK, unlike the full spec's `invoice_items` which snapshots.
- Router "Test Koneksi" must reject loopback/link-local/metadata-range hosts (resolved IP, not just the literal string) before attempting any connection, and the route is rate-limited `throttle:10,1`. Private LAN ranges (10/8, 172.16/12, 192.168/16) stay allowed.

## Review Focus

- **Cross-tenant ID guessing on edit/update/destroy/test-connection** for all three entities — tenant A, logged in, iterating numeric IDs for tenant B's rows must get 404, never the data or a successful mutation. Covered in Tasks 5, 6, 7 (one test per entity).
- **`ppp_username` collision scope** — the unique constraint is per `(tenant_id, router_id)`, not global: the same username on two *different* routers in the same tenant must be allowed; the same username twice on the *same* router must be rejected. Covered in Task 7.
- **SSRF via DNS resolution** — a *hostname* (not a literal IP) that resolves to a blocked range (e.g. `localtest.me` → `127.0.0.1`) must still be blocked; validating only the literal input string would miss this. Covered in Task 5.
- **Malformed CSV header** — an uploaded file missing a required column (e.g. "paket" column renamed/missing) must fail with one clear message, not silently treat every row's package lookup as blank. Covered in Task 8.
- **Mid-import duplicate blowing up the whole transaction** — a duplicate `ppp_username` appearing partway through a CSV must not surface as a raw 500; the transaction rolls back and the user gets one clear message. Covered in Task 8.

---

## Task 1: Router migration, model, factory

**Files:**
- Create: `database/migrations/2026_10_04_000001_create_routers_table.php`
- Create: `app/Modules/Master/Models/Router.php`
- Create: `database/factories/RouterFactory.php`
- Test: `tests/Feature/Master/RouterModelTest.php`

**Interfaces:**
- Produces: `App\Modules\Master\Models\Router` with fillable `tenant_id, name, network_driver, host, api_port, api_username_enc, api_password_enc, status, last_seen_at`; `Router::factory()`.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('name', 100);
            $table->string('network_driver', 30)->default('manual');
            $table->string('host', 150);
            $table->unsignedInteger('api_port')->default(8728);
            $table->text('api_username_enc')->nullable();
            $table->text('api_password_enc')->nullable();
            $table->string('status', 20)->default('unknown');
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE routers ADD CONSTRAINT ck_routers_driver CHECK (network_driver IN ('manual','mikrotik_pppoe'))");
        DB::statement("ALTER TABLE routers ADD CONSTRAINT ck_routers_status CHECK (status IN ('unknown','online','offline'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_10_04_000001_create_routers_table ... DONE` in the output.

- [ ] **Step 3: Write the model**

```php
<?php

namespace App\Modules\Master\Models;

use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\RouterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tenant_id', 'name', 'network_driver', 'host', 'api_port', 'api_username_enc', 'api_password_enc', 'status', 'last_seen_at'])]
#[Hidden(['api_username_enc', 'api_password_enc'])]
class Router extends Model
{
    use BelongsToTenant;
    /** @use HasFactory<RouterFactory> */
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return RouterFactory::new();
    }

    protected function casts(): array
    {
        return [
            'api_username_enc' => 'encrypted',
            'api_password_enc' => 'encrypted',
            'last_seen_at' => 'datetime',
        ];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(\App\Modules\Customer\Models\Customer::class);
    }
}
```

- [ ] **Step 4: Write the factory**

```php
<?php

namespace Database\Factories;

use App\Modules\Master\Models\Router;
use App\Modules\Platform\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Router>
 */
class RouterFactory extends Factory
{
    protected $model = Router::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => 'MT-'.fake()->unique()->word(),
            'network_driver' => 'manual',
            'host' => fake()->ipv4(),
            'api_port' => 8728,
            'status' => 'unknown',
        ];
    }
}
```

- [ ] **Step 5: Write the failing test**

```php
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
```

- [ ] **Step 6: Run the test**

Run: `vendor/bin/pest tests/Feature/Master/RouterModelTest.php`
Expected: both tests PASS.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_10_04_000001_create_routers_table.php app/Modules/Master/Models/Router.php database/factories/RouterFactory.php tests/Feature/Master/RouterModelTest.php
git commit -m "Add Router model, migration, factory"
```

---

## Task 2: Package migration, model, factory

**Files:**
- Create: `database/migrations/2026_10_04_000002_create_packages_table.php`
- Create: `app/Modules/Master/Models/Package.php`
- Create: `database/factories/PackageFactory.php`
- Test: `tests/Feature/Master/PackageModelTest.php`

**Interfaces:**
- Consumes: `App\Modules\Master\Models\Router` (Task 1).
- Produces: `App\Modules\Master\Models\Package` with fillable `tenant_id, router_id, name, speed_label, base_price, ppn_percent, default_profile, allow_online_registration, is_active`; method `totalPrice(): int` (computed, not stored); `Package::factory()`.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
            $table->string('name', 100);
            $table->string('speed_label', 40);
            $table->bigInteger('base_price');
            $table->decimal('ppn_percent', 5, 2)->default(11.00);
            $table->string('default_profile', 80)->nullable();
            $table->boolean('allow_online_registration')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE packages ADD CONSTRAINT ck_packages_base_price CHECK (base_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_10_04_000002_create_packages_table ... DONE`.

- [ ] **Step 3: Write the model**

```php
<?php

namespace App\Modules\Master\Models;

use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tenant_id', 'router_id', 'name', 'speed_label', 'base_price', 'ppn_percent', 'default_profile', 'allow_online_registration', 'is_active'])]
class Package extends Model
{
    use BelongsToTenant;
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return PackageFactory::new();
    }

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'ppn_percent' => 'decimal:2',
            'allow_online_registration' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(\App\Modules\Customer\Models\Customer::class);
    }

    /**
     * Derived, not stored: base_price + PPN, rounded to the nearest rupiah.
     * Keeping this computed avoids total_price drifting out of sync with
     * base_price/ppn_percent if either changes without a recompute step.
     */
    public function totalPrice(): int
    {
        return (int) round($this->base_price * (1 + (float) $this->ppn_percent / 100));
    }
}
```

- [ ] **Step 4: Write the factory**

```php
<?php

namespace Database\Factories;

use App\Modules\Master\Models\Package;
use App\Modules\Platform\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => 'Home '.fake()->randomElement([10, 20, 50]).' Mbps',
            'speed_label' => fake()->randomElement(['10 Mbps', '20 Mbps', '50 Mbps']),
            'base_price' => fake()->randomElement([100000, 150000, 250000]),
            'ppn_percent' => 11.00,
            'is_active' => true,
        ];
    }
}
```

- [ ] **Step 5: Write the failing test**

```php
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
```

- [ ] **Step 6: Run the test**

Run: `vendor/bin/pest tests/Feature/Master/PackageModelTest.php`
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_10_04_000002_create_packages_table.php app/Modules/Master/Models/Package.php database/factories/PackageFactory.php tests/Feature/Master/PackageModelTest.php
git commit -m "Add Package model, migration, factory"
```

---

## Task 3: Customer migration, model, factory

**Files:**
- Create: `database/migrations/2026_10_04_000003_create_customers_table.php`
- Create: `app/Modules/Customer/Models/Customer.php`
- Create: `database/factories/CustomerFactory.php`
- Test: `tests/Feature/Customer/CustomerModelTest.php`

**Interfaces:**
- Consumes: `App\Modules\Master\Models\Package`, `App\Modules\Master\Models\Router` (Tasks 1–2).
- Produces: `App\Modules\Customer\Models\Customer` with fillable `tenant_id, customer_code, name, phone, id_number_enc, address, package_id, router_id, ppp_username, ppp_password_enc, billing_type, due_day, extra_amount, discount, status`; soft-deletable; `Customer::factory()`.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('customer_code', 20);
            $table->string('name', 150);
            $table->string('phone', 20);
            $table->text('id_number_enc')->nullable();
            $table->string('address', 255)->nullable();
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
            $table->string('ppp_username', 80)->nullable();
            $table->text('ppp_password_enc')->nullable();
            $table->string('billing_type', 10)->default('postpaid');
            $table->unsignedTinyInteger('due_day')->nullable();
            $table->bigInteger('extra_amount')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->string('status', 12)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE customers ADD CONSTRAINT ck_customers_billing_type CHECK (billing_type IN ('postpaid','prepaid'))");
        DB::statement("ALTER TABLE customers ADD CONSTRAINT ck_customers_status CHECK (status IN ('active','suspended','stopped'))");

        DB::statement('CREATE UNIQUE INDEX uq_customers_code ON customers (tenant_id, customer_code) WHERE deleted_at IS NULL');
        DB::statement('CREATE UNIQUE INDEX uq_customers_ppp ON customers (tenant_id, router_id, ppp_username) WHERE deleted_at IS NULL AND ppp_username IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_10_04_000003_create_customers_table ... DONE`.

- [ ] **Step 3: Write the model**

```php
<?php

namespace App\Modules\Customer\Models;

use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'tenant_id', 'customer_code', 'name', 'phone', 'id_number_enc', 'address',
    'package_id', 'router_id', 'ppp_username', 'ppp_password_enc',
    'billing_type', 'due_day', 'extra_amount', 'discount', 'status',
])]
#[Hidden(['id_number_enc', 'ppp_password_enc'])]
class Customer extends Model
{
    use BelongsToTenant;
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, SoftDeletes;

    protected static function newFactory(): Factory
    {
        return CustomerFactory::new();
    }

    protected function casts(): array
    {
        return [
            'id_number_enc' => 'encrypted',
            'ppp_password_enc' => 'encrypted',
            'extra_amount' => 'integer',
            'discount' => 'integer',
            'due_day' => 'integer',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}
```

- [ ] **Step 4: Write the factory**

```php
<?php

namespace Database\Factories;

use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Platform\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_code' => 'C-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'package_id' => Package::factory(),
            'billing_type' => 'postpaid',
            'status' => 'active',
        ];
    }
}
```

- [ ] **Step 5: Write the failing test**

```php
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
```

- [ ] **Step 6: Run the test**

Run: `vendor/bin/pest tests/Feature/Customer/CustomerModelTest.php`
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_10_04_000003_create_customers_table.php app/Modules/Customer/Models/Customer.php database/factories/CustomerFactory.php tests/Feature/Customer/CustomerModelTest.php
git commit -m "Add Customer model, migration, factory"
```

---

## Task 4: Extend RLS security policy to the three new tables

**Files:**
- Create: `database/migrations/2026_10_04_000004_extend_rls_for_master_and_customer.php`
- Test: `tests/Feature/Tenancy/TenantIsolationRlsExtendedTest.php`

**Interfaces:**
- Consumes: `sec.tenant_policy`, `sec.fn_tenant_filter` (created in `database/migrations/2026_10_03_000005_add_rls_security_policy.php`); `App\Support\Tenancy\TenantScope` (for the `withoutGlobalScope` bypass in the test, same pattern as `tests/Feature/Tenancy/TenantIsolationRlsTest.php`).

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Same reason as 2026_10_03_000005: ALTER SECURITY POLICY takes a
     * schema-stability lock incompatible with Laravel's migration
     * transaction.
     */
    public $withinTransaction = false;

    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER SECURITY POLICY sec.tenant_policy
              ADD FILTER PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.routers,
              ADD BLOCK PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.routers AFTER INSERT,
              ADD FILTER PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.packages,
              ADD BLOCK PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.packages AFTER INSERT,
              ADD FILTER PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.customers,
              ADD BLOCK PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.customers AFTER INSERT
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER SECURITY POLICY sec.tenant_policy
              DROP FILTER PREDICATE ON dbo.routers,
              DROP BLOCK PREDICATE ON dbo.routers,
              DROP FILTER PREDICATE ON dbo.packages,
              DROP BLOCK PREDICATE ON dbo.packages,
              DROP FILTER PREDICATE ON dbo.customers,
              DROP BLOCK PREDICATE ON dbo.customers
        SQL);
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_10_04_000004_extend_rls_for_master_and_customer ... DONE`. If SQL Server reports the policy is already covering one of these tables or a syntax issue, double check table names are lowercase `dbo.routers`/`dbo.packages`/`dbo.customers` matching Task 1–3's `Schema::create` names exactly.

- [ ] **Step 3: Write the failing test**

```php
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
```

- [ ] **Step 4: Run the test**

Run: `vendor/bin/pest tests/Feature/Tenancy/TenantIsolationRlsExtendedTest.php`
Expected: PASS. If it fails with tenant A's row visible, the `ALTER SECURITY POLICY` in Step 1 didn't take — check `SELECT * FROM sys.security_policies` / `sys.security_predicates` on the DB to confirm predicates exist for `customers`.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_10_04_000004_extend_rls_for_master_and_customer.php tests/Feature/Tenancy/TenantIsolationRlsExtendedTest.php
git commit -m "Extend RLS security policy to routers, packages, customers"
```

---

## Task 5: Router CRUD + Test Koneksi (with SSRF guard)

**Files:**
- Create: `app/Modules/Master/Services/HostGuard.php`
- Create: `app/Modules/Master/Services/RouterConnectionResult.php`
- Create: `app/Modules/Master/Services/RouterConnectionTester.php`
- Create: `app/Modules/Master/Http/Requests/StoreRouterRequest.php`
- Create: `app/Modules/Master/Http/Requests/UpdateRouterRequest.php`
- Create: `app/Modules/Master/Http/Controllers/RouterController.php`
- Create: `resources/views/mockup/dashboard/routers-create.blade.php`
- Create: `resources/views/mockup/dashboard/routers-edit.blade.php`
- Modify: `resources/views/mockup/dashboard/routers.blade.php` (replace static array with `$routers` from controller)
- Modify: `routes/web.php` (insert router routes above the `/mockup/dashboard/{path}` catch-all)
- Test: `tests/Feature/Master/HostGuardTest.php`
- Test: `tests/Feature/Master/RouterConnectionTesterTest.php`
- Test: `tests/Feature/Master/RouterControllerTest.php`

**Interfaces:**
- Consumes: `App\Modules\Master\Models\Router` (Task 1); `App\Support\Tenancy\TenantContext` (existing).
- Produces: `HostGuard::isBlocked(string $ip): bool` (static, pure); `RouterConnectionResult` (readonly DTO: `reachable`, `blocked`, `message`, `latencyMs`); `RouterConnectionTester::test(string $host, int $port): RouterConnectionResult`, constructor accepts an optional `?\Closure $connector` for test injection. Named routes `routers.index`, `routers.create`, `routers.store`, `routers.edit`, `routers.update`, `routers.destroy`, `routers.test-connection`.

- [ ] **Step 1: Write the failing test for HostGuard (pure logic, no DB)**

```php
<?php

use App\Modules\Master\Services\HostGuard;

test('loopback and unspecified addresses are blocked', function () {
    expect(HostGuard::isBlocked('127.0.0.1'))->toBeTrue();
    expect(HostGuard::isBlocked('::1'))->toBeTrue();
    expect(HostGuard::isBlocked('0.0.0.0'))->toBeTrue();
});

test('link-local and cloud metadata addresses are blocked', function () {
    expect(HostGuard::isBlocked('169.254.169.254'))->toBeTrue();
});

test('private LAN ranges are allowed, matching how MikroTik routers are actually reached', function () {
    expect(HostGuard::isBlocked('10.0.0.5'))->toBeFalse();
    expect(HostGuard::isBlocked('172.16.0.5'))->toBeFalse();
    expect(HostGuard::isBlocked('192.168.1.1'))->toBeFalse();
});

test('public addresses are allowed', function () {
    expect(HostGuard::isBlocked('8.8.8.8'))->toBeFalse();
});
```

- [ ] **Step 2: Run it to verify it fails**

Run: `vendor/bin/pest tests/Feature/Master/HostGuardTest.php`
Expected: FAIL with "Class App\Modules\Master\Services\HostGuard not found".

- [ ] **Step 3: Write HostGuard**

```php
<?php

namespace App\Modules\Master\Services;

/**
 * Pure IP-range check, deliberately separate from RouterConnectionTester so
 * it's testable without opening real sockets. Private LAN ranges (10/8,
 * 172.16/12, 192.168/16) are intentionally NOT blocked: MikroTik routers
 * legitimately live there, reached over VPN, per
 * docs/spec/BACKEND-BILLINGIN-laravel-sqlserver.md §2.2. Blocking those
 * would break the real feature, not just close an attack surface.
 */
class HostGuard
{
    private const BLOCKED_CIDRS = [
        '127.0.0.0/8',
        '169.254.0.0/16',
        '0.0.0.0/32',
        '::1/128',
    ];

    public static function isBlocked(string $ip): bool
    {
        foreach (self::BLOCKED_CIDRS as $cidr) {
            if (self::ipInCidr($ip, $cidr)) {
                return true;
            }
        }

        return false;
    }

    private static function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);

        if (str_contains($ip, ':') !== str_contains($subnet, ':')) {
            return false; // IPv4 vs IPv6 mismatch, never a match
        }

        $ipBin = inet_pton($ip);
        $subnetBin = inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false) {
            return false;
        }

        $bits = (int) $bits;
        $bytes = intdiv($bits, 8);
        $remainder = $bits % 8;

        if ($bytes > 0 && substr($ipBin, 0, $bytes) !== substr($subnetBin, 0, $bytes)) {
            return false;
        }

        if ($remainder === 0) {
            return true;
        }

        $mask = ~(0xFF >> $remainder) & 0xFF;

        return (ord($ipBin[$bytes]) & $mask) === (ord($subnetBin[$bytes]) & $mask);
    }
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `vendor/bin/pest tests/Feature/Master/HostGuardTest.php`
Expected: all 4 tests PASS.

- [ ] **Step 5: Write the failing test for RouterConnectionTester**

```php
<?php

use App\Modules\Master\Services\RouterConnectionTester;

test('reports reachable when the connector succeeds', function () {
    $fakeHandle = fopen('php://memory', 'r');
    $tester = new RouterConnectionTester(fn ($ip, $port, $timeout) => $fakeHandle);

    $result = $tester->test('203.0.113.10', 8728);

    expect($result->reachable)->toBeTrue();
    expect($result->blocked)->toBeFalse();
});

test('reports unreachable when the connector fails, without being blocked', function () {
    $tester = new RouterConnectionTester(fn ($ip, $port, $timeout) => false);

    $result = $tester->test('203.0.113.10', 8728);

    expect($result->reachable)->toBeFalse();
    expect($result->blocked)->toBeFalse();
});

test('blocks a loopback host before ever calling the connector', function () {
    $called = false;
    $tester = new RouterConnectionTester(function (...$args) use (&$called) {
        $called = true;

        return false;
    });

    $result = $tester->test('127.0.0.1', 8728);

    expect($result->blocked)->toBeTrue();
    expect($called)->toBeFalse();
});

test('blocks a hostname that resolves to a blocked range, not just a literal blocked IP', function () {
    // localtest.me is a stable public DNS entry that resolves to 127.0.0.1 —
    // this is exactly the resolve-then-check bypass HostGuard alone can't
    // catch; RouterConnectionTester must resolve before checking.
    $called = false;
    $tester = new RouterConnectionTester(function (...$args) use (&$called) {
        $called = true;

        return false;
    });

    $result = $tester->test('localtest.me', 8728);

    expect($result->blocked)->toBeTrue();
    expect($called)->toBeFalse();
});
```

- [ ] **Step 6: Run it to verify it fails**

Run: `vendor/bin/pest tests/Feature/Master/RouterConnectionTesterTest.php`
Expected: FAIL with "Class ... RouterConnectionResult not found" (or similar — nothing exists yet).

- [ ] **Step 7: Write RouterConnectionResult and RouterConnectionTester**

```php
<?php

namespace App\Modules\Master\Services;

readonly class RouterConnectionResult
{
    public function __construct(
        public bool $reachable,
        public bool $blocked,
        public string $message,
        public ?int $latencyMs,
    ) {}
}
```

```php
<?php

namespace App\Modules\Master\Services;

class RouterConnectionTester
{
    public function __construct(private readonly ?\Closure $connector = null) {}

    public function test(string $host, int $port): RouterConnectionResult
    {
        $ip = $this->resolve($host);

        if ($ip === null) {
            return new RouterConnectionResult(false, true, 'Host tidak bisa di-resolve.', null);
        }

        if (HostGuard::isBlocked($ip)) {
            return new RouterConnectionResult(false, true, 'Host ini tidak diizinkan untuk diuji.', null);
        }

        $connect = $this->connector ?? function (string $ip, int $port, int $timeout) {
            $errno = 0;
            $errstr = '';

            return @fsockopen($ip, $port, $errno, $errstr, $timeout);
        };

        $start = microtime(true);
        $connection = $connect($ip, $port, 3);
        $latencyMs = (int) round((microtime(true) - $start) * 1000);

        if ($connection === false || $connection === null) {
            return new RouterConnectionResult(false, false, 'Tidak bisa terhubung.', $latencyMs);
        }

        if (is_resource($connection)) {
            fclose($connection);
        }

        return new RouterConnectionResult(true, false, 'Terhubung.', $latencyMs);
    }

    /**
     * IPv4-only resolution (gethostbyname): routers are given LAN IPv4
     * addresses in practice, and fsockopen below is tried against whatever
     * this returns — adding AAAA/IPv6 resolution is unnecessary complexity
     * for S1's actual use case.
     */
    private function resolve(string $host): ?string
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $host;
        }

        $ip = gethostbyname($host);

        return $ip === $host ? null : $ip;
    }
}
```

- [ ] **Step 8: Run the test to verify it passes**

Run: `vendor/bin/pest tests/Feature/Master/RouterConnectionTesterTest.php`
Expected: all 4 tests PASS. (The `localtest.me` test needs outbound DNS resolution to work in whatever environment runs it — if it fails specifically with "blocked" being false in an offline CI sandbox, that's an environment limitation, not a code bug; confirm manually with `php -r "var_dump(gethostbyname('localtest.me'));"` resolving to `127.0.0.1` where you do have network access.)

- [ ] **Step 9: Write the FormRequests**

```php
<?php

namespace App\Modules\Master\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRouterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'network_driver' => ['required', Rule::in(['manual', 'mikrotik_pppoe'])],
            'host' => ['required', 'string', 'max:150'],
            'api_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'api_username' => ['nullable', 'string', 'max:150'],
            'api_password' => ['nullable', 'string', 'max:150'],
        ];
    }
}
```

```php
<?php

namespace App\Modules\Master\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRouterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'network_driver' => ['required', Rule::in(['manual', 'mikrotik_pppoe'])],
            'host' => ['required', 'string', 'max:150'],
            'api_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'api_username' => ['nullable', 'string', 'max:150'],
            'api_password' => ['nullable', 'string', 'max:150'],
        ];
    }
}
```

(The unused `TenantContext` import is omitted here — `StoreRouterRequest`/`UpdateRouterRequest` don't need cross-table `exists` checks the way Package/Customer requests will in Tasks 6–7.)

- [ ] **Step 10: Write the controller**

```php
<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Master\Http\Requests\StoreRouterRequest;
use App\Modules\Master\Http\Requests\UpdateRouterRequest;
use App\Modules\Master\Models\Router;
use App\Modules\Master\Services\RouterConnectionTester;
use Illuminate\Http\JsonResponse;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::withCount('customers')->orderBy('name')->get();

        return view('mockup.dashboard.routers', ['routers' => $routers]);
    }

    public function create()
    {
        return view('mockup.dashboard.routers-create');
    }

    public function store(StoreRouterRequest $request)
    {
        $data = $request->safe()->except(['api_username', 'api_password']);
        $data['api_username_enc'] = $request->input('api_username');
        $data['api_password_enc'] = $request->input('api_password');

        Router::create($data);

        return redirect()->route('routers.index')->with('status', 'Router berhasil ditambahkan.');
    }

    public function edit(Router $router)
    {
        return view('mockup.dashboard.routers-edit', ['router' => $router]);
    }

    public function update(UpdateRouterRequest $request, Router $router)
    {
        $data = $request->safe()->except(['api_username', 'api_password']);

        if ($request->filled('api_username')) {
            $data['api_username_enc'] = $request->input('api_username');
        }
        if ($request->filled('api_password')) {
            $data['api_password_enc'] = $request->input('api_password');
        }

        $router->update($data);

        return redirect()->route('routers.index')->with('status', 'Router berhasil diperbarui.');
    }

    /**
     * Hard delete: customers.router_id is nullOnDelete (Task 3 migration),
     * so any customer still pointing at this router loses that association
     * instead of the delete being blocked. Acceptable for S1 — flagged here
     * for whoever builds the Router Sinkronkan-Secret/isolir flow in S3,
     * since that flow needs every customer to have a live router_id.
     */
    public function destroy(Router $router): \Illuminate\Http\RedirectResponse
    {
        $router->delete();

        return redirect()->route('routers.index')->with('status', 'Router dihapus.');
    }

    public function testConnection(Router $router, RouterConnectionTester $tester): JsonResponse
    {
        $result = $tester->test($router->host, $router->api_port);

        if ($result->blocked) {
            return response()->json(['ok' => false, 'message' => $result->message], 422);
        }

        $router->update([
            'status' => $result->reachable ? 'online' : 'offline',
            'last_seen_at' => $result->reachable ? now() : $router->last_seen_at,
        ]);

        return response()->json([
            'ok' => true,
            'reachable' => $result->reachable,
            'message' => $result->message,
            'latency_ms' => $result->latencyMs,
        ]);
    }
}
```

- [ ] **Step 11: Add routes**

Insert this block in `routes/web.php` directly above the line `Route::get('/mockup/dashboard/{path}', function (string $path) {` (the one preceded by the `// QA v2 S-01` comment). Add the `use` import at the top of the file alongside the existing `use Illuminate\Support\Facades\Route;`.

```php
use App\Modules\Master\Http\Controllers\RouterController;
```

```php
Route::middleware('auth')->group(function () {
    Route::get('/mockup/dashboard/routers', [RouterController::class, 'index'])->name('routers.index');
    Route::get('/mockup/dashboard/routers-create', [RouterController::class, 'create'])->name('routers.create');
    Route::post('/mockup/dashboard/routers', [RouterController::class, 'store'])->name('routers.store');
    Route::get('/mockup/dashboard/routers/{router}/edit', [RouterController::class, 'edit'])->name('routers.edit');
    Route::put('/mockup/dashboard/routers/{router}', [RouterController::class, 'update'])->name('routers.update');
    Route::delete('/mockup/dashboard/routers/{router}', [RouterController::class, 'destroy'])->name('routers.destroy');
    Route::post('/mockup/dashboard/routers/{router}/test-connection', [RouterController::class, 'testConnection'])
        ->middleware('throttle:10,1')->name('routers.test-connection');
});
```

- [ ] **Step 12: Write routers-create.blade.php**

```blade
@extends('mockup.dashboard.layout')

@section('title', 'Tambah Router — BILLINGIN')
@section('active', 'routers')
@section('back-link')
    <a href="{{ route('routers.index') }}" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Router</a>
@endsection
@section('page-title', 'Tambah Router')

@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300 max-w-2xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('routers.store') }}" class="space-y-6 max-w-2xl">
        @csrf
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama router</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="MT-Mekar-01" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Driver</label>
                    <select name="network_driver" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="manual" @selected(old('network_driver', 'manual') === 'manual')>Manual (belum tersambung otomatis)</option>
                        <option value="mikrotik_pppoe" @selected(old('network_driver') === 'mikrotik_pppoe')>MikroTik PPPoE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Host / IP</label>
                    <input type="text" name="host" value="{{ old('host') }}" required placeholder="192.168.88.1" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Port API</label>
                    <input type="number" name="api_port" value="{{ old('api_port', 8728) }}" required min="1" max="65535" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Username API (opsional)</label>
                    <input type="text" name="api_username" value="{{ old('api_username') }}" autocomplete="off" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Password API (opsional)</label>
                    <input type="password" name="api_password" autocomplete="new-password" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Router</button>
            <a href="{{ route('routers.index') }}" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
```

- [ ] **Step 13: Write routers-edit.blade.php**

```blade
@extends('mockup.dashboard.layout')

@section('title', 'Ubah Router — BILLINGIN')
@section('active', 'routers')
@section('back-link')
    <a href="{{ route('routers.index') }}" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Router</a>
@endsection
@section('page-title', 'Ubah Router')

@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300 max-w-2xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('routers.update', $router) }}" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama router</label>
                    <input type="text" name="name" value="{{ old('name', $router->name) }}" required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Driver</label>
                    <select name="network_driver" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="manual" @selected(old('network_driver', $router->network_driver) === 'manual')>Manual (belum tersambung otomatis)</option>
                        <option value="mikrotik_pppoe" @selected(old('network_driver', $router->network_driver) === 'mikrotik_pppoe')>MikroTik PPPoE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Host / IP</label>
                    <input type="text" name="host" value="{{ old('host', $router->host) }}" required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Port API</label>
                    <input type="number" name="api_port" value="{{ old('api_port', $router->api_port) }}" required min="1" max="65535" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Username API (kosongkan jika tidak diubah)</label>
                    <input type="text" name="api_username" autocomplete="off" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Password API (kosongkan jika tidak diubah)</label>
                    <input type="password" name="api_password" autocomplete="new-password" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Perubahan</button>
            <a href="{{ route('routers.index') }}" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
```

- [ ] **Step 14: Rewrite routers.blade.php to use real data**

Replace the entire `@section('content') ... @endsection` block (currently iterating the hardcoded `[... 'name' => 'MT-Mekar-01' ...]` array) with:

```blade
@section('page-actions')
    <a href="{{ route('routers.create') }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Router
    </a>
@endsection

@section('content')
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse ($routers as $router)
            <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6" data-router-id="{{ $router->id }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-lg"><i class="fa-solid fa-tower-broadcast"></i></span>
                        <div>
                            <p class="font-bold">{{ $router->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $router->host }}:{{ $router->api_port }} &middot; {{ $router->network_driver }}</p>
                        </div>
                    </div>
                    <span data-status-badge
                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $router->status === 'online' ? 'bg-emerald-500/15 text-emerald-400' : ($router->status === 'offline' ? 'bg-red-500/15 text-red-400' : 'bg-white/10 text-gray-400') }}">
                        &bull; {{ ['online' => 'Online', 'offline' => 'Offline', 'unknown' => 'Belum diuji'][$router->status] }}
                    </span>
                </div>
                <div class="border-t border-white/10 pt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Pelanggan terhubung</span>
                        <span class="font-semibold">{{ $router->customers_count }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Terakhir dicek</span>
                        <span class="font-semibold">{{ $router->last_seen_at?->format('d M Y, H.i') ?? '—' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-5 flex-wrap">
                    <button type="button" data-test-connection="{{ route('routers.test-connection', $router) }}" class="h-11 px-4 rounded-lg border border-white/15 font-semibold hover:bg-white/5">Uji Koneksi</button>
                    <button type="button" onclick="mockupToast('Sinkronisasi secret {{ $router->name }} belum tersedia — bagian S3')" class="h-11 px-4 rounded-lg border border-white/15 font-semibold hover:bg-white/5">Sinkronkan Secret</button>
                    <a href="{{ route('routers.edit', $router) }}" class="h-11 px-4 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Ubah</a>
                    <form method="POST" action="{{ route('routers.destroy', $router) }}" onsubmit="return confirm('Hapus router {{ $router->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="h-11 px-4 rounded-lg border border-red-500/30 text-red-300 font-semibold hover:bg-red-500/10">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
                <p class="font-bold mb-1">Belum ada router</p>
                <p class="text-gray-400 text-sm mb-5">Tambahkan router pertama untuk menghubungkan pelanggan ke jaringan.</p>
                <a href="{{ route('routers.create') }}" class="h-11 px-5 inline-flex items-center rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold">Tambah Router</a>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-test-connection]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('[data-router-id]');
            const badge = card.querySelector('[data-status-badge]');
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Menguji...';

            fetch(btn.dataset.testConnection, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
                .then(function (res) { return res.json().then(function (body) { return { status: res.status, body: body }; }); })
                .then(function (result) {
                    if (!result.body.ok) {
                        mockupToast(result.body.message);
                        return;
                    }
                    mockupToast(result.body.reachable
                        ? 'Terhubung (' + result.body.latency_ms + ' ms)'
                        : 'Tidak bisa terhubung: ' + result.body.message);
                    if (badge) {
                        badge.textContent = '• ' + (result.body.reachable ? 'Online' : 'Offline');
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-semibold ' +
                            (result.body.reachable ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400');
                    }
                })
                .catch(function () { mockupToast('Gagal menguji koneksi.'); })
                .finally(function () { btn.disabled = false; btn.textContent = originalText; });
        });
    });
</script>
@endsection
```

Leave the `@extends`/`@section('title', ...)`/`@section('active', 'routers')`/`@section('page-title', 'Router')`/`@section('page-subtitle')` lines at the top of the file untouched — only the old `@section('page-actions')` (the "Tambah Router belum tersedia" toast button) and the old `@section('content')` block are replaced by the two blocks above, and the new `@section('scripts')` block is appended at the end of the file.

Add `<meta name="csrf-token" content="{{ csrf_token() }}">` to `resources/views/mockup/dashboard/layout.blade.php`'s `<head>` if it isn't already there (check first — the fetch call above reads it as a fallback).

- [ ] **Step 15: Write the controller feature test**

```php
<?php

use App\Modules\Master\Models\Router;
use App\Modules\Master\Services\RouterConnectionResult;
use App\Modules\Master\Services\RouterConnectionTester;
use App\Modules\Platform\Models\Tenant;
use App\Modules\Identity\Models\User;
use App\Support\Tenancy\TenantContext;
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
    TenantContext::apply($tenantA->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create — actingAs() alone doesn't run SetTenantContext middleware (no HTTP request happens here)
    $routerA = Router::factory()->create(['tenant_id' => $tenantA->id]);

    $this->actingAs($userB)->get(route('routers.edit', $routerA))->assertNotFound();
    $this->actingAs($userB)->delete(route('routers.destroy', $routerA))->assertNotFound();
    // This checks the row's physical DB state (not deleted), not whether
    // the current viewer can see it. Bypassing the Eloquent scope alone
    // isn't enough: SQL Server's RLS FILTER PREDICATE is a separate,
    // DB-layer check against SESSION_CONTEXT('tenant_id'), still set to
    // tenant B from the last HTTP call above — it hides the row too,
    // independent of Eloquent. Switch to the superadmin session context
    // (same pattern as tests/Feature/Tenancy/TenantIsolationRlsTest.php)
    // to bypass both layers at once.
    TenantContext::apply(null, true);
    expect(Router::find($routerA->id))->not->toBeNull(); // untouched
});

test('test-connection endpoint rejects a blocked host without calling the tester twice or leaking a 500', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
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
    TenantContext::apply($tenant->id, false); // RLS block predicate needs SESSION_CONTEXT set for this direct Eloquent create
    $router = Router::factory()->create(['tenant_id' => $tenant->id, 'host' => '203.0.113.10']);

    $response = $this->actingAs($user)->postJson(route('routers.test-connection', $router));

    $response->assertOk();
    expect($response->json('reachable'))->toBeTrue();
    expect($router->fresh()->status)->toBe('online');
});
```

- [ ] **Step 16: Run all Task 5 tests**

Run: `vendor/bin/pest tests/Feature/Master/`
Expected: all PASS.

- [ ] **Step 17: Commit**

```bash
git add app/Modules/Master/Services app/Modules/Master/Http app/Modules/Master/Models/Router.php resources/views/mockup/dashboard/routers.blade.php resources/views/mockup/dashboard/routers-create.blade.php resources/views/mockup/dashboard/routers-edit.blade.php resources/views/mockup/dashboard/layout.blade.php routes/web.php tests/Feature/Master
git commit -m "Wire Router CRUD and SSRF-guarded Test Koneksi to real data"
```

---

## Task 6: Package CRUD (delete = deactivate)

**Files:**
- Create: `app/Modules/Master/Http/Requests/StorePackageRequest.php`
- Create: `app/Modules/Master/Http/Requests/UpdatePackageRequest.php`
- Create: `app/Modules/Master/Http/Controllers/PackageController.php`
- Create: `resources/views/mockup/dashboard/packages-create.blade.php`
- Create: `resources/views/mockup/dashboard/packages-edit.blade.php`
- Modify: `resources/views/mockup/dashboard/packages.blade.php` (replace static array with `$packages` from controller)
- Modify: `routes/web.php` (insert package routes above the `/mockup/dashboard/{path}` catch-all, same anchor as Task 5)
- Test: `tests/Feature/Master/PackageControllerTest.php`

**Interfaces:**
- Consumes: `App\Modules\Master\Models\Package`, `App\Modules\Master\Models\Router` (Tasks 1–2).
- Produces: Named routes `packages.index`, `packages.create`, `packages.store`, `packages.edit`, `packages.update`, `packages.destroy` (destroy = deactivate, see Step 7).

- [ ] **Step 1: Write the FormRequests**

```php
<?php

namespace App\Modules\Master\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'speed_label' => ['required', 'string', 'max:40'],
            'base_price' => ['required', 'integer', 'min:0'],
            'ppn_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'router_id' => ['nullable', 'integer', Rule::exists('routers', 'id')->where('tenant_id', TenantContext::tenantId())],
            'default_profile' => ['nullable', 'string', 'max:80'],
            'allow_online_registration' => ['sometimes', 'boolean'],
        ];
    }
}
```

```php
<?php

namespace App\Modules\Master\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'speed_label' => ['required', 'string', 'max:40'],
            'base_price' => ['required', 'integer', 'min:0'],
            'ppn_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'router_id' => ['nullable', 'integer', Rule::exists('routers', 'id')->where('tenant_id', TenantContext::tenantId())],
            'default_profile' => ['nullable', 'string', 'max:80'],
            'allow_online_registration' => ['sometimes', 'boolean'],
        ];
    }
}
```

- [ ] **Step 2: Write the controller**

```php
<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Master\Http\Requests\StorePackageRequest;
use App\Modules\Master\Http\Requests\UpdatePackageRequest;
use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('customers')->with('router')->orderBy('name')->get();

        return view('mockup.dashboard.packages', ['packages' => $packages]);
    }

    public function create()
    {
        return view('mockup.dashboard.packages-create', ['routers' => Router::orderBy('name')->get()]);
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        Package::create($request->validated());

        return redirect()->route('packages.index')->with('status', 'Paket berhasil ditambahkan.');
    }

    public function edit(Package $package)
    {
        return view('mockup.dashboard.packages-edit', [
            'package' => $package,
            'routers' => Router::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $package->update($request->validated());

        return redirect()->route('packages.index')->with('status', 'Paket berhasil diperbarui.');
    }

    /**
     * Never a hard delete: customers.package_id is a required, live FK
     * (unlike invoice_items in the full spec, which snapshots price at
     * invoice time) — removing the row would orphan every customer still
     * on this package. "Hapus" in the UI deactivates instead; inactive
     * packages drop out of the Tambah/Ubah Pelanggan dropdowns but existing
     * customers referencing them are unaffected.
     */
    public function destroy(Package $package): RedirectResponse
    {
        $package->update(['is_active' => false]);

        return redirect()->route('packages.index')->with('status', 'Paket dinonaktifkan.');
    }
}
```

- [ ] **Step 3: Add routes**

Insert into the same `Route::middleware('auth')->group(function () { ... });` block added in Task 5, Step 11 (above the `/mockup/dashboard/{path}` catch-all), alongside the router routes:

```php
use App\Modules\Master\Http\Controllers\PackageController;
```

```php
    Route::get('/mockup/dashboard/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/mockup/dashboard/packages-create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/mockup/dashboard/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/mockup/dashboard/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/mockup/dashboard/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/mockup/dashboard/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
```

- [ ] **Step 4: Write packages-create.blade.php**

```blade
@extends('mockup.dashboard.layout')

@section('title', 'Tambah Paket — BILLINGIN')
@section('active', 'packages')
@section('back-link')
    <a href="{{ route('packages.index') }}" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Paket</a>
@endsection
@section('page-title', 'Tambah Paket')

@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300 max-w-2xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('packages.store') }}" class="space-y-6 max-w-2xl">
        @csrf
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama paket</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Home 20 Mbps" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Label kecepatan</label>
                    <input type="text" name="speed_label" value="{{ old('speed_label') }}" required placeholder="20 Mbps" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Harga dasar (Rp)</label>
                    <input type="number" name="base_price" value="{{ old('base_price') }}" required min="0" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">PPN (%)</label>
                    <input type="number" step="0.01" name="ppn_percent" value="{{ old('ppn_percent', 11) }}" required min="0" max="100" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Router (opsional)</label>
                    <select name="router_id" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Tidak terikat router —</option>
                        @foreach ($routers as $router)
                            <option value="{{ $router->id }}" @selected(old('router_id') == $router->id)>{{ $router->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Profile PPPoE default (opsional)</label>
                    <input type="text" name="default_profile" value="{{ old('default_profile') }}" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm mt-5">
                <input type="checkbox" name="allow_online_registration" value="1" checked class="w-4 h-4 rounded text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                Izinkan untuk pendaftaran online
            </label>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Paket</button>
            <a href="{{ route('packages.index') }}" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
```

- [ ] **Step 5: Write packages-edit.blade.php**

Same form as Step 4, with these changes: `action="{{ route('packages.update', $package) }}"`, add `@method('PUT')` right after `@csrf`, every `old('x')` becomes `old('x', $package->x)` (and `old('router_id')` becomes `old('router_id', $package->router_id)`), and the checkbox becomes `<input type="checkbox" name="allow_online_registration" value="1" @checked(old('allow_online_registration', $package->allow_online_registration)) ...>`. Button label "Simpan Perubahan" instead of "Simpan Paket".

- [ ] **Step 6: Rewrite packages.blade.php to use real data**

Replace `@section('page-actions')` (currently the "Fitur tambah paket belum tersedia" toast button) with:

```blade
@section('page-actions')
    <a href="{{ route('packages.create') }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Paket
    </a>
@endsection
```

Replace the `@section('content') ... @endsection` block (the `<table>` with the hardcoded `@foreach ([...] as $pkg)` array) with:

```blade
@section('content')
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('status') }}</div>
    @endif

    <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b border-white/10">
                    <th class="px-6 py-3 font-medium">Paket</th>
                    <th class="px-3 py-3 font-medium">Kecepatan</th>
                    <th class="px-3 py-3 font-medium">Harga dasar</th>
                    <th class="px-3 py-3 font-medium">Total / bulan</th>
                    <th class="px-3 py-3 font-medium">Pelanggan</th>
                    <th class="px-3 py-3 font-medium">Daftar online</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($packages as $pkg)
                    <tr class="hover:bg-white/5">
                        <td class="px-6 py-4 font-semibold">{{ $pkg->name }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg->speed_label }}</td>
                        <td class="px-3 py-4 text-gray-300">Rp {{ number_format($pkg->base_price, 0, ',', '.') }}</td>
                        <td class="px-3 py-4 font-semibold">Rp {{ number_format($pkg->totalPrice(), 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg->customers_count }}</td>
                        <td class="px-3 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pkg->allow_online_registration ? 'bg-emerald-500/15 text-emerald-400' : 'bg-white/10 text-gray-400' }}">&bull; {{ $pkg->allow_online_registration ? 'Ya' : 'Tidak' }}</span>
                        </td>
                        <td class="px-3 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pkg->is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-white/10 text-gray-400' }}">&bull; {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('packages.edit', $pkg) }}" class="h-9 px-4 inline-flex items-center rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Ubah</a>
                            @if ($pkg->is_active)
                                <form method="POST" action="{{ route('packages.destroy', $pkg) }}" class="inline" onsubmit="return confirm('Nonaktifkan paket {{ $pkg->name }}? Pelanggan yang masih memakainya tidak terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 px-4 rounded-lg border border-red-500/30 text-red-300 text-xs font-semibold hover:bg-red-500/10">Nonaktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">Belum ada paket. <a href="{{ route('packages.create') }}" class="text-indigo-400 hover:text-indigo-300">Tambah paket pertama</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
```

(Note the "Nonaktifkan" button label, not "Hapus" — matches the actual behavior per the destroy() note in Step 2, rather than the generic "Hapus" the spec flagged as potentially misleading.)

- [ ] **Step 7: Write the failing tests**

```php
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
```

- [ ] **Step 8: Run the tests**

Run: `vendor/bin/pest tests/Feature/Master/PackageControllerTest.php`
Expected: all 4 PASS.

- [ ] **Step 9: Commit**

```bash
git add app/Modules/Master/Http/Requests/StorePackageRequest.php app/Modules/Master/Http/Requests/UpdatePackageRequest.php app/Modules/Master/Http/Controllers/PackageController.php resources/views/mockup/dashboard/packages.blade.php resources/views/mockup/dashboard/packages-create.blade.php resources/views/mockup/dashboard/packages-edit.blade.php routes/web.php tests/Feature/Master/PackageControllerTest.php
git commit -m "Wire Package CRUD to real data (delete deactivates, never hard-deletes)"
```

---

## Task 7: Customer CRUD

**Files:**
- Create: `app/Modules/Customer/Http/Requests/StoreCustomerRequest.php`
- Create: `app/Modules/Customer/Http/Requests/UpdateCustomerRequest.php`
- Create: `app/Modules/Customer/Http/Controllers/CustomerController.php`
- Create: `resources/views/mockup/dashboard/customers-edit.blade.php`
- Modify: `resources/views/mockup/dashboard/customers.blade.php` (replace static array with `$customers` from controller)
- Modify: `resources/views/mockup/dashboard/customers-create.blade.php` (replace hardcoded `<option>` lists with `$packages`/`$routers`, point `action`/`onsubmit` at the real route)
- Modify: `routes/web.php` (insert customer routes into the same auth group as Tasks 5–6)
- Test: `tests/Feature/Customer/CustomerControllerTest.php`

**Interfaces:**
- Consumes: `App\Modules\Customer\Models\Customer` (Task 3), `App\Modules\Master\Models\Package`, `App\Modules\Master\Models\Router` (Tasks 1–2).
- Produces: Named routes `customers.index`, `customers.create`, `customers.store`, `customers.edit`, `customers.update`, `customers.destroy`.

- [ ] **Step 1: Write the FormRequests**

```php
<?php

namespace App\Modules\Customer\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'regex:/^(08|628)[0-9]{8,13}$/'],
            'id_number' => ['nullable', 'digits:16'],
            'address' => ['nullable', 'string', 'max:255'],
            'package_id' => [
                'required', 'integer',
                Rule::exists('packages', 'id')->where('tenant_id', TenantContext::tenantId())->where('is_active', true),
            ],
            'router_id' => ['nullable', 'integer', Rule::exists('routers', 'id')->where('tenant_id', TenantContext::tenantId())],
            'ppp_username' => [
                'nullable', 'string', 'max:80',
                Rule::unique('customers', 'ppp_username')
                    ->where('tenant_id', TenantContext::tenantId())
                    ->where('router_id', $this->input('router_id'))
                    ->whereNull('deleted_at'),
            ],
            'billing_type' => ['required', Rule::in(['postpaid', 'prepaid'])],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:28'],
            'extra_amount' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
```

```php
<?php

namespace App\Modules\Customer\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'regex:/^(08|628)[0-9]{8,13}$/'],
            'id_number' => ['nullable', 'digits:16'],
            'address' => ['nullable', 'string', 'max:255'],
            'package_id' => [
                'required', 'integer',
                Rule::exists('packages', 'id')->where('tenant_id', TenantContext::tenantId())->where('is_active', true),
            ],
            'router_id' => ['nullable', 'integer', Rule::exists('routers', 'id')->where('tenant_id', TenantContext::tenantId())],
            'ppp_username' => [
                'nullable', 'string', 'max:80',
                Rule::unique('customers', 'ppp_username')
                    ->where('tenant_id', TenantContext::tenantId())
                    ->where('router_id', $this->input('router_id'))
                    ->whereNull('deleted_at')
                    ->ignore($this->route('customer')),
            ],
            'billing_type' => ['required', Rule::in(['postpaid', 'prepaid'])],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:28'],
            'extra_amount' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
```

- [ ] **Step 2: Write the controller**

```php
<?php

namespace App\Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customer\Http\Requests\StoreCustomerRequest;
use App\Modules\Customer\Http\Requests\UpdateCustomerRequest;
use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['package', 'router'])->orderBy('name')->get();

        return view('mockup.dashboard.customers', ['customers' => $customers]);
    }

    public function create()
    {
        return view('mockup.dashboard.customers-create', [
            'packages' => Package::where('is_active', true)->orderBy('name')->get(),
            'routers' => Router::orderBy('name')->get(),
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['id_number']);
        $data['id_number_enc'] = $request->input('id_number');
        $data['due_day'] ??= 10; // S1 hardcoded fallback — see spec's "customer_code" note on tenants.due_day_default not being wired up here yet

        $attempts = 0;
        while (true) {
            $data['customer_code'] = $this->nextCustomerCode();
            try {
                Customer::create($data);
                break;
            } catch (QueryException $e) {
                if ($e->getCode() !== '23000' || ++$attempts >= 3) {
                    throw $e;
                }
            }
        }

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('mockup.dashboard.customers-edit', [
            'customer' => $customer,
            'packages' => Package::where('is_active', true)->orderBy('name')->get(),
            'routers' => Router::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->safe()->except(['id_number']);

        if ($request->filled('id_number')) {
            $data['id_number_enc'] = $request->input('id_number');
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Pelanggan dihapus.');
    }

    /**
     * Sequential per tenant, formatted C-1000, C-1001, ... The unique DB
     * index (uq_customers_code, Task 3 migration) is the real guard; a
     * collision here just retries (caught in store() above) since this is
     * a low-traffic admin form, not a high-concurrency path.
     */
    private function nextCustomerCode(): string
    {
        return 'C-'.(1000 + Customer::withTrashed()->count());
    }
}
```

- [ ] **Step 3: Add routes**

Insert into the same `Route::middleware('auth')->group(function () { ... });` block from Tasks 5–6:

```php
use App\Modules\Customer\Http\Controllers\CustomerController;
```

```php
    Route::get('/mockup/dashboard/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/mockup/dashboard/customers-create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/mockup/dashboard/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/mockup/dashboard/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/mockup/dashboard/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/mockup/dashboard/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
```

- [ ] **Step 4: Wire customers-create.blade.php to real data**

In `resources/views/mockup/dashboard/customers-create.blade.php`:

Replace:
```blade
<form id="customer-form" action="#" onsubmit="return false" class="space-y-6 max-w-4xl">
```
with:
```blade
<form id="customer-form" method="POST" action="{{ route('customers.store') }}" class="space-y-6 max-w-4xl">
    @csrf
```

Replace the "Paket" `<select>` block:
```blade
<select required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <option>Home 10 Mbps &middot; Rp 111.000</option>
    <option>Home 20 Mbps &middot; Rp 166.500</option>
    <option>Home 50 Mbps &middot; Rp 277.500</option>
</select>
```
with:
```blade
<select name="package_id" required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <option value="">— Pilih paket —</option>
    @foreach ($packages as $package)
        <option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>{{ $package->name }} &middot; Rp {{ number_format($package->totalPrice(), 0, ',', '.') }}</option>
    @endforeach
</select>
```

Replace the "Router" `<select>` block:
```blade
<select required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <option>MT-Mekar-01</option>
    <option>MT-Kampung-02</option>
</select>
```
with:
```blade
<select name="router_id" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <option value="">— Tidak dipasang router —</option>
    @foreach ($routers as $router)
        <option value="{{ $router->id }}" @selected(old('router_id') == $router->id)>{{ $router->name }}</option>
    @endforeach
</select>
```

Add `name="name"` to the "Nama lengkap" input, `name="phone"` to "Nomor WhatsApp", `name="id_number"` to "NIK", `name="address"` to "Alamat pemasangan", `name="ppp_username"` to "Username PPPoE", remove the "Password PPPoE" input (not in S1 scope — PPPoE password provisioning is an S3 concern once a real NetworkDriver exists; leaving an unused field inviting a false impression it does something would be worse than dropping it), `name="due_day"` type `number` instead of the "Tanggal isolir otomatis" `<select>` of canned dates (replace its three `<option>`s with a plain `<input type="number" name="due_day" min="1" max="28" placeholder="10">`), `name="extra_amount"` on "Biaya tambahan (Rp)", `name="discount"` on "Diskon (Rp)", `name="billing_type"` with `value="postpaid"`/`value="prepaid"` on the two radio inputs (the "Kirim tagihan lewat WhatsApp" checkbox stays decorative — WhatsApp sending is S4, out of scope).

Replace the submit button:
```blade
<button type="submit" onclick="if (document.getElementById('customer-form').reportValidity()) { mockupToast('Fitur simpan pelanggan belum tersedia di mockup ini') }" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Pelanggan</button>
```
with:
```blade
<button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Pelanggan</button>
```

Add an error summary block right after `@section('content')`:
```blade
@if ($errors->any())
    <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300 max-w-4xl">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

- [ ] **Step 5: Write customers-edit.blade.php**

Copy the now-updated `customers-create.blade.php` from Step 4 and change: title/page-title to "Ubah Pelanggan", `action="{{ route('customers.update', $customer) }}"` with `@method('PUT')` added after `@csrf`, every input's `old('x')` becomes `old('x', $customer->x)` (`old('package_id', $customer->package_id)`, `old('router_id', $customer->router_id)`, plain value attributes for `name`/`phone`/`address`/`ppp_username`/`due_day`/`extra_amount`/`discount` become `value="{{ old('x', $customer->x) }}"`, the billing_type radios get `@checked(old('billing_type', $customer->billing_type) === 'postpaid')` / `'prepaid'`), and the NIK field stays empty by default (never pre-fill a decrypted NIK into a form field) with a placeholder "Kosongkan jika tidak diubah". Button label "Simpan Perubahan".

- [ ] **Step 6: Rewrite customers.blade.php to use real data**

Replace the `@php $customers = [...]; $statusClass = [...]; @endphp` block and the `@foreach ($customers as $c)` row markup with:

```blade
@php
    $statusClass = [
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'suspended' => 'bg-red-500/15 text-red-400',
        'stopped' => 'bg-white/10 text-gray-400',
    ];
    $statusLabel = ['active' => 'Aktif', 'suspended' => 'Terisolir', 'stopped' => 'Berhenti'];
@endphp
@foreach ($customers as $c)
    <tr class="hover:bg-white/5">
        <td class="px-6 py-4 text-gray-400">{{ $c->customer_code }}</td>
        <td class="px-3 py-4">
            <p class="font-semibold">{{ $c->name }}</p>
            <p class="text-gray-500 text-xs">{{ $c->phone }}</p>
        </td>
        <td class="px-3 py-4 text-gray-300">{{ $c->package->name }}</td>
        <td class="px-3 py-4 text-gray-300">{{ $c->router->name ?? '—' }}</td>
        <td class="px-3 py-4">
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass[$c->status] }}">&bull; {{ $statusLabel[$c->status] }}</span>
        </td>
        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
            <a href="{{ route('customers.edit', $c) }}" class="h-9 px-4 inline-flex items-center rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Ubah</a>
            <form method="POST" action="{{ route('customers.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus pelanggan {{ $c->name }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-9 px-4 rounded-lg border border-red-500/30 text-red-300 text-xs font-semibold hover:bg-red-500/10">Hapus</button>
            </form>
        </td>
    </tr>
@endforeach
```

The table header row loses the "Area" and "Jatuh tempo"/"Tagihan" `<th>` columns (those depend on Invoice, which doesn't exist until S2) and gains a "Router" column — update the `<thead>` to: ID, Pelanggan, Paket, Router, Status, Aksi. The pagination footer ("Menampilkan 8 dari 150 pelanggan" / "Berikutnya" toast stub) and the state-demo switcher stay as-is — real pagination is out of S1 scope.

Also replace `@section('page-subtitle')`'s "data contoh" badge removal isn't required — leave it; it's harmless once real data loads underneath it, and removing it is cosmetic scope creep not asked for.

- [ ] **Step 7: Write the failing tests**

```php
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
```

- [ ] **Step 8: Run the tests**

Run: `vendor/bin/pest tests/Feature/Customer/CustomerControllerTest.php`
Expected: all 6 PASS.

- [ ] **Step 9: Commit**

```bash
git add app/Modules/Customer/Http resources/views/mockup/dashboard/customers.blade.php resources/views/mockup/dashboard/customers-create.blade.php resources/views/mockup/dashboard/customers-edit.blade.php routes/web.php tests/Feature/Customer/CustomerControllerTest.php
git commit -m "Wire Customer CRUD to real data with tenant-scoped validation"
```

---

## Task 8: Customer CSV import

**Files:**
- Create: `app/Modules/Customer/Http/Controllers/CustomerImportController.php`
- Modify: `resources/views/mockup/dashboard/customers-import.blade.php` (real upload form + result summary)
- Modify: `routes/web.php` (insert import routes into the same auth group)
- Test: `tests/Feature/Customer/CustomerImportControllerTest.php`

**Interfaces:**
- Consumes: `App\Modules\Customer\Models\Customer`, `App\Modules\Master\Models\Package` (Tasks 2–3).
- Produces: Named routes `customers.import.create`, `customers.import.store`.

CSV format (header row required, case-insensitive, matches the existing page copy "nama, nomor WhatsApp, alamat, paket, username PPPoE"): columns `nama`, `wa`, `alamat`, `paket`, `username_pppoe`. `alamat` is optional per row; the other three plus `paket` matching an active package by exact name (case-insensitive) are required.

- [ ] **Step 1: Write the controller**

```php
<?php

namespace App\Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customer\Models\Customer;
use App\Modules\Master\Models\Package;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerImportController extends Controller
{
    public function create()
    {
        return view('mockup.dashboard.customers-import');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $normalizedHeader = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $header ?: []);

        $required = ['nama', 'wa', 'paket', 'username_pppoe'];
        $missing = array_diff($required, $normalizedHeader);

        if ($missing !== []) {
            fclose($handle);

            return back()->withErrors([
                'csv_file' => 'Kolom wajib tidak ada di header CSV: '.implode(', ', $missing).'.',
            ]);
        }

        $columnIndex = array_flip($normalizedHeader);
        $packagesByName = Package::where('is_active', true)->get()->keyBy(fn ($p) => mb_strtolower($p->name));

        $rows = [];
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            $name = trim((string) ($row[$columnIndex['nama']] ?? ''));
            $phone = trim((string) ($row[$columnIndex['wa']] ?? ''));
            $packageName = trim((string) ($row[$columnIndex['paket']] ?? ''));
            $pppUsername = trim((string) ($row[$columnIndex['username_pppoe']] ?? ''));
            $address = isset($columnIndex['alamat']) ? trim((string) ($row[$columnIndex['alamat']] ?? '')) : null;

            if ($name === '') {
                $errors[] = "Baris {$rowNumber}: nama kosong.";

                continue;
            }
            if (! preg_match('/^(08|628)[0-9]{8,13}$/', $phone)) {
                $errors[] = "Baris {$rowNumber}: nomor WA \"{$phone}\" tidak valid.";

                continue;
            }
            $package = $packagesByName->get(mb_strtolower($packageName));
            if (! $package) {
                $errors[] = "Baris {$rowNumber}: paket \"{$packageName}\" tidak ditemukan atau nonaktif.";

                continue;
            }

            $rows[] = [
                'name' => $name,
                'phone' => $phone,
                'address' => $address !== '' ? $address : null,
                'package_id' => $package->id,
                'ppp_username' => $pppUsername !== '' ? $pppUsername : null,
                'billing_type' => 'postpaid',
                'status' => 'active',
            ];
        }
        fclose($handle);

        $created = 0;

        try {
            DB::transaction(function () use ($rows, &$created) {
                foreach ($rows as $row) {
                    $row['customer_code'] = 'C-'.(1000 + Customer::withTrashed()->count());
                    Customer::create($row);
                    $created++;
                }
            });
        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return back()->withErrors([
                'csv_file' => 'Impor dibatalkan: ada baris dengan data duplikat (kemungkinan username PPPoE sudah dipakai di router yang sama). Tidak ada baris yang disimpan — perbaiki file dan unggah ulang.',
            ]);
        }

        return back()->with('import_summary', ['created' => $created, 'errors' => $errors]);
    }
}
```

- [ ] **Step 2: Add routes**

Insert into the same `Route::middleware('auth')->group(function () { ... });` block from Tasks 5–7:

```php
use App\Modules\Customer\Http\Controllers\CustomerImportController;
```

```php
    Route::get('/mockup/dashboard/customers-import', [CustomerImportController::class, 'create'])->name('customers.import.create');
    Route::post('/mockup/dashboard/customers-import', [CustomerImportController::class, 'store'])->name('customers.import.store');
```

- [ ] **Step 3: Wire customers-import.blade.php to the real endpoint**

Replace:
```blade
<form action="#" onsubmit="return false">
    <label for="csv-file" class="block border-2 border-dashed border-white/15 rounded-xl py-14 text-center cursor-pointer hover:border-indigo-500/50 hover:bg-white/5 transition">
        <span class="block text-3xl mb-3">⬆</span>
        <span class="block font-semibold">Seret file ke sini atau klik untuk memilih</span>
        <span class="block text-gray-500 text-sm mt-1">Format .csv, maksimal 5 MB</span>
        <input id="csv-file" type="file" accept=".csv" class="hidden">
    </label>
    <p id="csv-filename" class="hidden text-sm text-gray-300 mt-3"></p>
    <button id="csv-import-btn" type="button" onclick="mockupToast('Impor CSV belum tersedia di mockup ini')" class="hidden mt-4 h-11 px-5 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Impor</button>
</form>
```
with:
```blade
@if ($errors->any())
    <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (session('import_summary'))
    @php $summary = session('import_summary'); @endphp
    <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
        {{ $summary['created'] }} pelanggan berhasil diimpor.
    </div>
    @if (count($summary['errors']))
        <div class="mb-5 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
            <p class="font-semibold mb-2">{{ count($summary['errors']) }} baris dilewati:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($summary['errors'] as $rowError)
                    <li>{{ $rowError }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endif

<form method="POST" action="{{ route('customers.import.store') }}" enctype="multipart/form-data">
    @csrf
    <label for="csv-file" class="block border-2 border-dashed border-white/15 rounded-xl py-14 text-center cursor-pointer hover:border-indigo-500/50 hover:bg-white/5 transition">
        <span class="block text-3xl mb-3">⬆</span>
        <span class="block font-semibold">Seret file ke sini atau klik untuk memilih</span>
        <span class="block text-gray-500 text-sm mt-1">Format .csv, maksimal 5 MB</span>
        <input id="csv-file" name="csv_file" type="file" accept=".csv" required class="hidden">
    </label>
    <p id="csv-filename" class="hidden text-sm text-gray-300 mt-3"></p>
    <button id="csv-import-btn" type="submit" class="mt-4 h-11 px-5 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Impor</button>
</form>
```

(The existing `@section('scripts')` block with the `change` listener on `#csv-file` that shows the filename and un-hides the button stays exactly as-is — it already does the right thing for a real `<input type="file">`.)

- [ ] **Step 4: Write the failing tests**

```php
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
```

- [ ] **Step 5: Run the tests**

Run: `vendor/bin/pest tests/Feature/Customer/CustomerImportControllerTest.php`
Expected: all 3 PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Modules/Customer/Http/Controllers/CustomerImportController.php resources/views/mockup/dashboard/customers-import.blade.php routes/web.php tests/Feature/Customer/CustomerImportControllerTest.php
git commit -m "Wire Customer CSV import: synchronous, per-row errors, no partial-duplicate 500"
```

---

## Task 9: Final route ordering check, full suite, manual verification, deploy

**Files:**
- Modify: none (verification-only task)

**Interfaces:**
- Consumes: everything from Tasks 1–8.

- [ ] **Step 1: Confirm route registration order**

Run: `php artisan route:list --path=mockup/dashboard`
Expected: every explicit route added in Tasks 5–8 (`customers.*`, `packages.*`, `routers.*`) appears, and `GET|HEAD /mockup/dashboard/{path}` (the generic catch-all) is listed but never intercepts them — Laravel always matches the most specific route regardless of declaration order for static vs wildcard segments, but confirm by actually hitting a URL in Step 3 below rather than trusting the list alone.

- [ ] **Step 2: Run the full test suite**

Run: `vendor/bin/pest`
Expected: every test in `tests/Feature/Master`, `tests/Feature/Customer`, and all pre-existing tests (`tests/Feature/Auth`, `tests/Feature/Tenancy`) PASS. Zero regressions in the tests that existed before this plan.

- [ ] **Step 3: Manual verification — single tenant**

1. `docker compose up -d --build app worker scheduler` (rebuild — the app container has no bind-mount; `restart` alone serves stale code, per the deploy history for this project).
2. Log in as `admin@tenant-a.test` / `password`.
3. Go to `/mockup/dashboard/packages-create`, submit a package. Confirm it appears in `/mockup/dashboard/packages` after reload (not just in the current page — reload the browser tab).
4. Go to `/mockup/dashboard/routers-create`, submit a router. Confirm it appears in `/mockup/dashboard/routers` after reload. Click "Uji Koneksi" — confirm it returns reachable/unreachable without a 500, and the status badge updates.
5. Go to `/mockup/dashboard/customers-create`, submit a customer using the package/router just created. Confirm it appears in `/mockup/dashboard/customers` after reload.
6. Edit that customer (new `/mockup/dashboard/customers/{id}/edit` page), change the name, save, confirm the change persisted after reload.
7. Delete the package created in step 3 via the "Nonaktifkan" button — confirm the package list shows it as "Nonaktif" (not gone), and the customer from step 5 still shows its package name correctly in the customer list.

- [ ] **Step 4: Manual verification — tenant isolation**

1. Log out, log in as `admin@tenant-b.test` / `password` (same browser tab, per the QA agent's note that it can't type passwords itself — this step needs a human).
2. Confirm `/mockup/dashboard/customers`, `/mockup/dashboard/packages`, `/mockup/dashboard/routers` show **zero** of tenant A's data from Step 3.
3. Try navigating directly to tenant A's customer edit URL from Step 3.6 (copy the numeric ID) while logged in as tenant B — confirm a 404 page, not the customer's data.

- [ ] **Step 5: Notify QA**

Message the QA agent that S1 is deployed to `103.191.92.163:8080` and ready for the full scenario list from the QA prompt (paths now match what's actually live: `/mockup/dashboard/packages-create`, `/mockup/dashboard/routers-create`, `/mockup/dashboard/customers/{id}/edit`, `/mockup/dashboard/packages/{id}/edit`, `/mockup/dashboard/routers/{id}/edit`, `/mockup/dashboard/routers/{id}/test-connection`).

- [ ] **Step 6: Commit (if Step 3's manual pass surfaced any fixes)**

```bash
git add -A
git commit -m "S1 fixes from manual verification pass"
```

(Skip this commit entirely if Step 3 found nothing to fix.)

