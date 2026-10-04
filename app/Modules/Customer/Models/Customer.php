<?php

namespace App\Modules\Customer\Models;

use App\Modules\Master\Models\Package;
use App\Modules\Master\Models\Router;
use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'tenant_id', 'customer_code', 'name', 'phone', 'id_number_enc', 'address',
    'package_id', 'router_id', 'ppp_username', 'ppp_password_enc',
    'billing_type', 'due_day', 'extra_amount', 'discount', 'status',
])]
#[Hidden(['id_number_enc', 'ppp_password_enc'])]
class Customer extends Model
{
    use BelongsToTenant;
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, SoftDeletes;

    protected static function newFactory(): Factory
    {
        return CustomerFactory::new();
    }

    protected function casts(): array
    {
        return [
            'id_number_enc' => 'encrypted',
            'ppp_password_enc' => 'encrypted',
            'extra_amount' => 'integer',
            'discount' => 'integer',
            'due_day' => 'integer',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}
