<?php

namespace App\Policies;

use App\Models\User;
use Modules\Audit\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('it.role.manage');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('it.role.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('it.role.manage');
    }

    public function update(User $user, Role $role): bool
    {
        return $role->code !== 'owner'
            && $user->hasPermission('it.role.manage');
    }
}
