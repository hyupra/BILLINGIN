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
