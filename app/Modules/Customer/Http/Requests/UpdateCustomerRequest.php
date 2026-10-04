<?php

namespace App\Modules\Customer\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Package must be active EXCEPT when it's the customer's own current
        // package — otherwise deactivating a package locks out editing any
        // other field of every customer still assigned to it.
        $customer = $this->route('customer');
        $packageExists = Rule::exists('packages', 'id')->where('tenant_id', TenantContext::tenantId());
        if ((int) $this->input('package_id') !== (int) $customer?->package_id) {
            $packageExists->where('is_active', true);
        }

        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'regex:/^(08|628)[0-9]{8,13}$/'],
            'id_number' => ['nullable', 'digits:16'],
            'address' => ['nullable', 'string', 'max:255'],
            'package_id' => ['required', 'integer', $packageExists],
            'router_id' => ['nullable', 'integer', Rule::exists('routers', 'id')->where('tenant_id', TenantContext::tenantId())],
            'ppp_username' => [
                'nullable', 'string', 'max:80',
                Rule::unique('customers', 'ppp_username')
                    ->where('tenant_id', TenantContext::tenantId())
                    ->where('router_id', $this->input('router_id'))
                    ->whereNull('deleted_at')
                    ->ignore($this->route('customer')),
            ],
            'billing_type' => ['required', Rule::in(['postpaid', 'prepaid'])],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:28'],
            'extra_amount' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
