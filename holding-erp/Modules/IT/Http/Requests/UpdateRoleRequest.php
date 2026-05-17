<?php

namespace Modules\IT\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('it.role.manage') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'code' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('roles', 'code')->ignore($role?->id)],
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
