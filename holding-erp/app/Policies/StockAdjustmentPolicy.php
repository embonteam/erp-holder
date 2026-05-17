<?php

namespace App\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockAdjustment;

class StockAdjustmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('inventory.stock.view');
    }

    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->hasPermission('inventory.stock.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('inventory.adjustment.create');
    }

    public function approve(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $stockAdjustment->status === 'draft'
            && $user->hasPermission('inventory.adjustment.approve');
    }
}
