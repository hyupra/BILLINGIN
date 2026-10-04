<?php

namespace App\Modules\Master\Models;

use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\RouterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tenant_id', 'name', 'network_driver', 'host', 'api_port', 'api_username_enc', 'api_password_enc', 'status', 'last_seen_at'])]
#[Hidden(['api_username_enc', 'api_password_enc'])]
class Router extends Model
{
    use BelongsToTenant;
    /** @use HasFactory<RouterFactory> */
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return RouterFactory::new();
    }

    protected function casts(): array
    {
        return [
            'api_username_enc' => 'encrypted',
            'api_password_enc' => 'encrypted',
            'last_seen_at' => 'datetime',
        ];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(\App\Modules\Customer\Models\Customer::class);
    }
}
