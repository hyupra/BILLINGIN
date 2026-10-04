<?php

namespace App\Modules\Master\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'speed_label' => ['required', 'string', 'max:40'],
            'base_price' => ['required', 'integer', 'min:0'],
            'ppn_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'router_id' => ['nullable', 'integer', Rule::exists('routers', 'id')->where('tenant_id', TenantContext::tenantId())],
            'default_profile' => ['nullable', 'string', 'max:80'],
            'allow_online_registration' => ['sometimes', 'boolean'],
        ];
    }
}
