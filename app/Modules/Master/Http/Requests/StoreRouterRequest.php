<?php

namespace App\Modules\Master\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRouterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'network_driver' => ['required', Rule::in(['manual', 'mikrotik_pppoe'])],
            'host' => ['required', 'string', 'max:150'],
            'api_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'api_username' => ['nullable', 'string', 'max:150'],
            'api_password' => ['nullable', 'string', 'max:150'],
        ];
    }
}
