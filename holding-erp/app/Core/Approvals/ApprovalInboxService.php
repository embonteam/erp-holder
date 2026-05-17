<?php

namespace App\Core\Approvals;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Purchasing\Models\Purchase;

class ApprovalInboxService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function forUser(User $user): Collection
    {
        return collect()
            ->when($user->hasPermission('purchasing.purchase.approve'), function (Collection $items): Collection {
                return $items->merge($this->purchaseApprovals());
            })
            ->sortByDesc('created_at')
            ->values();
    }

    public function countForUser(User $user): int
    {
        return $this->forUser($user)->count();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function purchaseApprovals(): Collection
    {
        return Purchase::query()
            ->with(['supplier', 'items'])
            ->where('status', 'draft')
            ->latest()
            ->get()
            ->map(fn (Purchase $purchase): array => [
                'type' => 'purchase_order',
                'severity' => 'warning',
                'title' => 'Purchase Order menunggu approval',
                'reference' => $purchase->po_number,
                'description' => sprintf(
                    '%s - %s item - Rp%s',
                    $purchase->supplier?->name ?? 'Supplier',
                    $purchase->items->count(),
                    number_format((float) $purchase->total_amount, 0, ',', '.'),
                ),
                'route' => route('purchasing.purchases.show', $purchase),
                'created_at' => $purchase->created_at,
            ]);
    }
}
