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
