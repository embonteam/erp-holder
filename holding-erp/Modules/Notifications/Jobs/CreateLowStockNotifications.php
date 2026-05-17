<?php

namespace Modules\Notifications\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Notifications\Models\EnterpriseNotification;

class CreateLowStockNotifications implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $warehouseStockId)
    {
    }

    public function handle(): void
    {
        $stock = WarehouseStock::query()
            ->with(['product', 'warehouse'])
            ->findOrFail($this->warehouseStockId);

        $recipients = User::query()
            ->where('is_active', true)
            ->whereHas('role.permissions', function ($query): void {
                $query->whereIn('code', [
                    'inventory.stock.view',
                    'purchasing.purchase.view',
                ]);
            })
            ->get();

        foreach ($recipients as $recipient) {
            EnterpriseNotification::query()->create([
                'user_id' => $recipient->id,
                'type' => 'inventory.low_stock',
                'severity' => 'warning',
                'title' => 'Low stock alert',
                'message' => sprintf(
                    '%s di %s tersisa %s.',
                    $stock->product?->name ?? 'Produk',
                    $stock->warehouse?->name ?? 'warehouse',
                    number_format((float) $stock->on_hand, 2),
                ),
                'payload' => [
                    'warehouse_stock_id' => $stock->id,
                    'warehouse_id' => $stock->warehouse_id,
                    'product_id' => $stock->product_id,
                    'on_hand' => (float) $stock->on_hand,
                    'reorder_level' => (float) $stock->reorder_level,
                ],
            ]);
        }
    }
}
