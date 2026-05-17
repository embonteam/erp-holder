<?php

namespace Modules\Purchasing\Services;

use App\Core\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Services\StockPostingService;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\PurchaseItem;
use Modules\Purchasing\Models\Supplier;

class PurchaseService
{
    public function __construct(
        private readonly StockPostingService $stockPostingService,
        private readonly DocumentNumberService $documentNumberService,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function createDraft(array $payload): Purchase
    {
        return DB::transaction(function () use ($payload): Purchase {
            $warehouse = Warehouse::query()->findOrFail($payload['warehouse_id']);
            $supplier = Supplier::query()->where('is_active', true)->findOrFail($payload['supplier_id']);
            $items = collect($payload['items']);

            if ((int) $supplier->holding_id !== (int) $warehouse->holding_id) {
                throw new InvalidArgumentException('Supplier and warehouse must belong to the same holding.');
            }

            $productBrandById = Product::query()
                ->whereIn('id', $items->pluck('product_id'))
                ->pluck('brand_id', 'id');

            foreach ($items as $item) {
                $productBrandId = $productBrandById->get($item['product_id']);

                if ($productBrandId === null) {
                    throw new InvalidArgumentException('Purchase item contains an unknown product.');
                }

                if ((int) $productBrandId !== (int) $warehouse->brand_id) {
                    throw new InvalidArgumentException('Purchase item product must belong to the destination warehouse brand.');
                }
            }

            $subtotal = $items->sum(function (array $item): float {
                return (float) $item['quantity'] * (float) $item['unit_price'];
            });
            $taxAmount = $items->sum(function (array $item): float {
                return ((float) $item['quantity'] * (float) $item['unit_price'])
                    * (((float) ($item['tax_rate'] ?? 0)) / 100);
            });

            $purchase = Purchase::query()->create([
                'brand_id' => $warehouse->brand_id,
                'city_id' => $warehouse->city_id,
                'branch_id' => $warehouse->branch_id,
                'warehouse_id' => $warehouse->id,
                'supplier_id' => $supplier->id,
                'po_number' => $payload['po_number'] ?? $this->documentNumberService->next('PO', [
                    'brand_id' => $warehouse->brand_id,
                    'city_id' => $warehouse->city_id,
                    'branch_id' => $warehouse->branch_id,
                    'warehouse_id' => $warehouse->id,
                ]),
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal + $taxAmount,
                'ordered_at' => $payload['ordered_at'] ?? now(),
                'requested_by' => $payload['requested_by'] ?? null,
            ]);

            foreach ($items as $item) {
                PurchaseItem::query()->create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'line_total' => (float) $item['quantity'] * (float) $item['unit_price'],
                ]);
            }

            return $purchase->refresh();
        });
    }

    public function approve(Purchase $purchase, ?int $approvedBy = null): Purchase
    {
        if ($purchase->status !== 'draft') {
            throw new InvalidArgumentException('Only draft purchases may be approved.');
        }

        $purchase->forceFill([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ])->save();

        return $purchase->refresh();
    }

    public function receive(Purchase $purchase): Purchase
    {
        if ($purchase->status !== 'approved') {
            throw new InvalidArgumentException('Only approved purchases may be received.');
        }

        return DB::transaction(function () use ($purchase): Purchase {
            $purchase->loadMissing('items');

            foreach ($purchase->items as $item) {
                $this->stockPostingService->post([
                    'brand_id' => $purchase->brand_id,
                    'city_id' => $purchase->city_id,
                    'branch_id' => $purchase->branch_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'product_id' => $item->product_id,
                    'movement_type' => 'purchase',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_price,
                    'source_type' => Purchase::class,
                    'source_id' => $purchase->id,
                    'occurred_at' => now(),
                ]);

                $item->forceFill([
                    'received_quantity' => $item->quantity,
                ])->save();
            }

            $purchase->forceFill([
                'status' => 'received',
            ])->save();

            return $purchase->refresh();
        });
    }
}
