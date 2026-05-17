<?php

namespace App\Policies;

use App\Models\User;
use Modules\Inventory\Models\StockTransfer;

class StockTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('inventory.stock.view');
    }

    public function view(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermission('inventory.stock.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('inventory.transfer.create');
    }

    public function approve(User $user, StockTransfer $stockTransfer): bool
    {
        return $stockTransfer->status === 'draft'
            && $user->hasPermission('inventory.transfer.approve');
    }

    public function dispatch(User $user, StockTransfer $stockTransfer): bool
    {
        return $stockTransfer->status === 'approved'
            && $user->hasPermission('inventory.transfer.dispatch');
    }

    public function receive(User $user, StockTransfer $stockTransfer): bool
    {
        return $stockTransfer->status === 'dispatched'
            && $user->hasPermission('inventory.transfer.receive');
    }
}
