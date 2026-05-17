<?php

namespace App\Policies;

use App\Models\User;
use Modules\Purchasing\Models\Purchase;

class PurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('purchasing.purchase.view');
    }

    public function view(User $user, Purchase $purchase): bool
    {
        return $user->hasPermission('purchasing.purchase.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('purchasing.purchase.create');
    }

    public function approve(User $user, Purchase $purchase): bool
    {
        return $purchase->status === 'draft'
            && $user->hasPermission('purchasing.purchase.approve');
    }

    public function receive(User $user, Purchase $purchase): bool
    {
        return $purchase->status === 'approved'
            && $user->hasPermission('purchasing.purchase.receive');
    }
}
