<?php

namespace App\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockOpname;

class StockOpnamePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('inventory.stock.view');
    }

    public function view(User $user, StockOpname $stockOpname): bool
    {
        return $user->hasPermission('inventory.stock.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('inventory.opname.create');
    }

    public function approve(User $user, StockOpname $stockOpname): bool
    {
        return $stockOpname->status === 'draft'
            && $user->hasPermission('inventory.opname.approve');
    }
}
