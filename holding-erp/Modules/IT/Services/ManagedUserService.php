<?php

namespace Modules\IT\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\HoldingCityPosition;
use Modules\Holding\Models\Warehouse;

class ManagedUserService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): User
    {
        $this->validateScopeChain($payload);

        return User::query()->create($this->userPayload($payload));
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(User $user, array $payload): User
    {
        $this->validateScopeChain($payload);

        $attributes = $this->userPayload($payload);

        if (($payload['password'] ?? null) === null || $payload['password'] === '') {
            unset($attributes['password']);
        }

        $user->fill($attributes)->save();

        return $user->refresh();
    }

    public function setActive(User $user, bool $active): User
    {
        $user->forceFill(['is_active' => $active])->save();

        return $user->refresh();
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function userPayload(array $payload): array
    {
        return Arr::only($payload, [
            'name',
            'email',
            'password',
            'role_id',
            'holding_id',
            'holding_city_position_id',
            'brand_id',
            'city_id',
            'branch_id',
            'warehouse_id',
        ]) + ['is_active' => $payload['is_active'] ?? true];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function validateScopeChain(array $payload): void
    {
        $holdingId = (int) $payload['holding_id'];

        if (! empty($payload['holding_city_position_id'])) {
            $region = HoldingCityPosition::query()->findOrFail($payload['holding_city_position_id']);

            if ((int) $region->holding_id !== $holdingId) {
                $this->scopeError('holding_city_position_id', 'Regional holding harus berada di holding yang sama.');
            }
        }

        if (! empty($payload['brand_id'])) {
            $brand = Brand::query()->findOrFail($payload['brand_id']);

            if ((int) $brand->holding_id !== $holdingId) {
                $this->scopeError('brand_id', 'Brand harus berada di holding yang sama.');
            }
        }

        if (! empty($payload['city_id'])) {
            $city = City::query()->findOrFail($payload['city_id']);

            if ((int) $city->holding_id !== $holdingId) {
                $this->scopeError('city_id', 'City harus berada di holding yang sama.');
            }
        }

        if (! empty($payload['branch_id'])) {
            $branch = Branch::query()->withoutGlobalScopes()->findOrFail($payload['branch_id']);

            if ((int) $branch->holding_id !== $holdingId) {
                $this->scopeError('branch_id', 'Branch harus berada di holding yang sama.');
            }

            if (! empty($payload['brand_id']) && (int) $branch->brand_id !== (int) $payload['brand_id']) {
                $this->scopeError('branch_id', 'Branch harus sesuai dengan brand user.');
            }

            if (! empty($payload['city_id']) && (int) $branch->city_id !== (int) $payload['city_id']) {
                $this->scopeError('branch_id', 'Branch harus sesuai dengan city user.');
            }
        }

        if (! empty($payload['warehouse_id'])) {
            $warehouse = Warehouse::query()->withoutGlobalScopes()->findOrFail($payload['warehouse_id']);

            if ((int) $warehouse->holding_id !== $holdingId) {
                $this->scopeError('warehouse_id', 'Warehouse harus berada di holding yang sama.');
            }

            if (! empty($payload['brand_id']) && (int) $warehouse->brand_id !== (int) $payload['brand_id']) {
                $this->scopeError('warehouse_id', 'Warehouse harus sesuai dengan brand user.');
            }

            if (! empty($payload['city_id']) && (int) $warehouse->city_id !== (int) $payload['city_id']) {
                $this->scopeError('warehouse_id', 'Warehouse harus sesuai dengan city user.');
            }

            if (! empty($payload['branch_id']) && (int) $warehouse->branch_id !== (int) $payload['branch_id']) {
                $this->scopeError('warehouse_id', 'Warehouse harus sesuai dengan branch user.');
            }
        }
    }

    private function scopeError(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
