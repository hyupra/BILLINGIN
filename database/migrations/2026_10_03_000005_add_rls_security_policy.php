<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * CREATE SCHEMA/FUNCTION must be the only statement in their batch, and a
     * security policy change takes a schema-stability lock — run outside
     * Laravel's automatic migration transaction.
     */
    public $withinTransaction = false;

    public function up(): void
    {
        DB::unprepared('CREATE SCHEMA sec');

        DB::unprepared(<<<'SQL'
            CREATE FUNCTION sec.fn_tenant_filter(@tenant_id BIGINT)
            RETURNS TABLE WITH SCHEMABINDING AS
            RETURN SELECT 1 AS ok
            WHERE @tenant_id = CAST(SESSION_CONTEXT(N'tenant_id') AS BIGINT)
               OR CAST(SESSION_CONTEXT(N'is_superadmin') AS INT) = 1
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE SECURITY POLICY sec.tenant_policy
              ADD FILTER PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.audit_logs,
              ADD BLOCK PREDICATE sec.fn_tenant_filter(tenant_id) ON dbo.audit_logs AFTER INSERT
              WITH (STATE = ON)
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP SECURITY POLICY IF EXISTS sec.tenant_policy');
        DB::unprepared('DROP FUNCTION IF EXISTS sec.fn_tenant_filter');
        DB::unprepared('DROP SCHEMA IF EXISTS sec');
    }
};
