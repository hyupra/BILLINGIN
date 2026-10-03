<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Superadmin role has no team (platform-level, tenant_id null).
        Role::findOrCreate('superadmin', 'web');
    }

    /**
     * admin_isp is per-tenant (spatie teams), so it must be created with the
     * tenant context already set — called from TenantSeeder per tenant
     * rather than once here.
     */
    public static function ensureAdminIspRole(): Role
    {
        return Role::findOrCreate('admin_isp', 'web');
    }
}
