<?php

namespace Database\Factories;

use App\Modules\Identity\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
            'subject_type' => 'App\\Modules\\Identity\\Models\\AuditLog',
            'subject_id' => fake()->numberBetween(1, 1000),
            'ip' => fake()->ipv4(),
            'created_at' => now(),
        ];
    }
}
