<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\User;
use App\Modules\Platform\Models\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        collect(['tenant-a', 'tenant-b'])->each(function (string $slug) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => $slug],
                [
                    'business_name' => 'ISP '.ucfirst(str_replace('tenant-', '', $slug)),
                    'status' => 'active',
                ],
            );

            $admin = User::firstOrCreate(
                ['email' => "admin@{$slug}.test"],
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Admin '.$tenant->business_name,
                    'password' => 'password',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );

            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            RolePermissionSeeder::ensureAdminIspRole();
            $admin->assignRole('admin_isp');
        });
    }
}
