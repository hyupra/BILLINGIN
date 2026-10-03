<?php

namespace App\Support\Tenancy;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class TenantContext
{
    private static ?int $tenantId = null;

    private static bool $isSuperadmin = false;

    public static function apply(?int $tenantId, bool $isSuperadmin): void
    {
        self::$tenantId = $tenantId;
        self::$isSuperadmin = $isSuperadmin;

        // tenant_id is bound inline, not as a parameter: the sqlsrv ODBC driver
        // fails to bind a NULL into sp_set_session_context's sql_variant @value
        // ("invalid parameter or option"). Safe to inline because this is
        // always a system-derived int|null (the authenticated user's
        // tenant_id), never raw request input.
        $tenantIdSql = $tenantId === null ? 'NULL' : (int) $tenantId;
        DB::statement("EXEC sp_set_session_context @key=N'tenant_id', @value={$tenantIdSql}");
        DB::statement("EXEC sp_set_session_context @key=N'is_superadmin', @value=?", [$isSuperadmin ? 1 : 0]);

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);
    }

    public static function tenantId(): ?int
    {
        return self::$tenantId;
    }

    public static function isSuperadmin(): bool
    {
        return self::$isSuperadmin;
    }
}
