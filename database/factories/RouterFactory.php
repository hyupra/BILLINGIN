<?php

namespace Database\Factories;

use App\Modules\Master\Models\Router;
use App\Modules\Platform\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Router>
 */
class RouterFactory extends Factory
{
    protected $model = Router::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => 'MT-'.fake()->unique()->word(),
            'network_driver' => 'manual',
            'host' => fake()->ipv4(),
            'api_port' => 8728,
            'status' => 'unknown',
        ];
    }
}
