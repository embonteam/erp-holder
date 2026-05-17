<?php

namespace Modules\IT\Services;

use Illuminate\Support\Facades\DB;
use Modules\Audit\Models\Role;

class RoleManagementService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): Role
    {
        return DB::transaction(function () use ($payload): Role {
            $role = Role::query()->create([
                'code' => $payload['code'],
                'name' => $payload['name'],
                'scope_level' => $payload['scope_level'],
                'is_system' => false,
            ]);

            $role->permissions()->sync($payload['permissions'] ?? []);

            return $role->refresh()->load('permissions');
        });
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(Role $role, array $payload): Role
    {
        return DB::transaction(function () use ($role, $payload): Role {
            $role->fill([
                'code' => $role->is_system ? $role->code : $payload['code'],
                'name' => $payload['name'],
                'scope_level' => $payload['scope_level'],
            ])->save();

            $role->permissions()->sync($payload['permissions'] ?? []);

            return $role->refresh()->load('permissions');
        });
    }
}
