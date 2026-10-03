<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\User;
use Illuminate\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    /**
     * Superadmin does NOT get a spatie role assignment: in teams mode,
     * model_has_roles makes team_foreign_key part of its primary key (not
     * nullable), so a role can't be assigned "with no team" — but
     * superadmin is deliberately tenant_id = null. Authorization for
     * superadmin relies on the users.is_superadmin column instead, which
     * exists for exactly this. The 'superadmin' role (seeded in
     * RolePermissionSeeder) stays unused here but available if a future
     * sprint wants permission-based checks for it.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@billingin.test'],
            [
                'tenant_id' => null,
                'name' => 'Superadmin',
                'password' => 'password',
                'is_superadmin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
