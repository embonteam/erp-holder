<?php

namespace App\Core\Authorization;

use Modules\Audit\Models\Permission;

trait HasPermissions
{
    public function hasPermission(string $permission): bool
    {
        return $this->role?->permissions()
            ->where('code', $permission)
            ->exists() ?? false;
    }

    /**
     * @param array<int, string> $permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->role?->permissions()
            ->whereIn('code', $permissions)
            ->exists() ?? false;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Permission>
     */
    public function permissions()
    {
        return $this->role?->permissions ?? collect();
    }
}
