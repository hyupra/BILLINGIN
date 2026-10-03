<?php

namespace App\Modules\Platform\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug', 'business_name', 'owner_name', 'whatsapp', 'email', 'timezone',
    'ppn_percent', 'rounding_step', 'due_day_default', 'grace_days',
    'first_invoice_mode', 'reminder_rules_json', 'status', 'trial_ends_at',
])]
class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return TenantFactory::new();
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'ppn_percent' => 'decimal:2',
        ];
    }
}
