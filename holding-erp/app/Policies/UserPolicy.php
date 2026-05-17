<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('it.user.manage');
    }

    public function view(User $user, User $managedUser): bool
    {
        return $user->hasPermission('it.user.manage')
            && $this->withinHolding($user, $managedUser);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('it.user.manage');
    }

    public function update(User $user, User $managedUser): bool
    {
        return $user->hasPermission('it.user.manage')
            && $this->withinHolding($user, $managedUser);
    }

    public function deactivate(User $user, User $managedUser): bool
    {
        return $user->id !== $managedUser->id
            && $user->hasPermission('it.user.manage')
            && $this->withinHolding($user, $managedUser);
    }

    public function reactivate(User $user, User $managedUser): bool
    {
        return $this->deactivate($user, $managedUser);
    }

    private function withinHolding(User $user, User $managedUser): bool
    {
        return $user->holding_id === null
            || $managedUser->holding_id === null
            || (int) $user->holding_id === (int) $managedUser->holding_id;
    }
}
