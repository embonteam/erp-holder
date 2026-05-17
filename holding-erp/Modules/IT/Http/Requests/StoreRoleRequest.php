<?php

namespace Modules\IT\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('it.role.manage') ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:roles,code'],
            'name' => ['required', 'string', 'max:255'],
            'scope_level' => ['required', 'string', 'in:holding,city,brand,branch,warehouse'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge([
                'code' => str($this->input('code'))->trim()->lower()->replace(' ', '_')->toString(),
            ]);
        }
    }
}
