<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        TenantContext::apply(
            $user?->tenant_id,
            (bool) $user?->is_superadmin,
        );

        return $next($request);
    }
}
