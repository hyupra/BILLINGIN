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
