<?php

namespace Modules\IT\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('it.user.manage') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'holding_id' => ['required', 'integer', 'exists:holdings,id'],
            'holding_city_position_id' => ['nullable', 'integer', 'exists:holding_city_positions,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->nullableIntegerFields());
    }

    /**
     * @return array<string, int|null>
     */
    private function nullableIntegerFields(): array
    {
        return collect(['holding_city_position_id', 'brand_id', 'city_id', 'branch_id', 'warehouse_id'])
            ->mapWithKeys(fn (string $field): array => [$field => $this->input($field) === '' ? null : $this->input($field)])
            ->all();
    }
}
