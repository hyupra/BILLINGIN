<?php

namespace App\Support\Tenancy;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if ($model->tenant_id === null && ! TenantContext::isSuperadmin()) {
                $model->tenant_id = TenantContext::tenantId();
            }
        });
    }
}
