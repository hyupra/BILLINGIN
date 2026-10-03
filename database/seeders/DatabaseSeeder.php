<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * WithoutModelEvents is deliberately not used here: BelongsToTenant
     * relies on the `creating` model event to auto-fill tenant_id, and
     * disabling events would silently break that for any future seeder
     * that leans on it instead of setting tenant_id explicitly.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperadminSeeder::class,
            TenantSeeder::class,
        ]);
    }
}
