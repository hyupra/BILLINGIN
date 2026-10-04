<?php

namespace App\Modules\Master\Models;

use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tenant_id', 'router_id', 'name', 'speed_label', 'base_price', 'ppn_percent', 'default_profile', 'allow_online_registration', 'is_active'])]
class Package extends Model
{
    use BelongsToTenant;
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return PackageFactory::new();
    }

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'ppn_percent' => 'decimal:2',
            'router_id' => 'integer',
            'allow_online_registration' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(\App\Modules\Customer\Models\Customer::class);
    }

    /**
     * Derived, not stored: base_price + PPN, rounded to the nearest rupiah.
     * Keeping this computed avoids total_price drifting out of sync with
     * base_price/ppn_percent if either changes without a recompute step.
     */
    public function totalPrice(): int
    {
        return (int) round($this->base_price * (1 + (float) $this->ppn_percent / 100));
    }
}
