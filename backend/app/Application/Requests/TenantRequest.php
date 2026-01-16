<?php

declare(strict_types=1);

namespace App\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = $this->route('tenant');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenants,email,' . $tenantId],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'status' => ['boolean'],
            'plan' => ['nullable', 'string', 'in:basic,premium,enterprise'],
            'domain' => ['nullable', 'string', 'max:255', 'unique:domains,domain'],
            'data' => ['nullable', 'array'],
            'seed' => ['boolean'],
        ];
    }
}
