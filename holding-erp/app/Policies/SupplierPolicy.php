<?php

namespace App\Policies;

use App\Models\User;
use Modules\Purchasing\Models\Supplier;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('purchasing.supplier.view');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission('purchasing.supplier.view')
            && $this->withinHolding($user, $supplier);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('purchasing.supplier.create');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission('purchasing.supplier.update')
            && $this->withinHolding($user, $supplier);
    }

    public function deactivate(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission('purchasing.supplier.deactivate')
            && $this->withinHolding($user, $supplier);
    }

    public function reactivate(User $user, Supplier $supplier): bool
    {
        return $this->deactivate($user, $supplier);
    }

    private function withinHolding(User $user, Supplier $supplier): bool
    {
        return $user->holding_id === null || (int) $user->holding_id === (int) $supplier->holding_id;
    }
}
