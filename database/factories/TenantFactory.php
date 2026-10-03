<?php

namespace Database\Factories;

use App\Modules\Platform\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $business = fake()->unique()->company();

        return [
            'slug' => Str::slug($business).'-'.fake()->unique()->numberBetween(100, 999),
            'business_name' => $business,
            'owner_name' => fake()->name(),
            'whatsapp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->companyEmail(),
            'status' => 'active',
        ];
    }
}
