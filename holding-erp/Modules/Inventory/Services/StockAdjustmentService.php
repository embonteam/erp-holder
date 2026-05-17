<?php

namespace Modules\Inventory\Services;

use App\Core\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockAdjustmentItem;

class StockAdjustmentService
{
    public function __construct(
        private readonly StockPostingService $stockPostingService,
        private readonly DocumentNumberService $documentNumberService,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function createDraft(array $payload): StockAdjustment
    {
        return DB::transaction(function () use ($payload): StockAdjustment {
            $warehouse = Warehouse::query()->findOrFail($payload['warehouse_id']);
            $items = collect($payload['items']);

            $this->assertProductsBelongToWarehouseBrand($items->pluck('product_id')->all(), $warehouse->brand_id);

            $adjustment = StockAdjustment::query()->create([
                'brand_id' => $warehouse->brand_id,
                'city_id' => $warehouse->city_id,
                'branch_id' => $warehouse->branch_id,
                'warehouse_id' => $warehouse->id,
                'adjustment_number' => $payload['adjustment_number'] ?? $this->documentNumberService->next('ADJ', [
                    'brand_id' => $warehouse->brand_id,
                    'city_id' => $warehouse->city_id,
                    'branch_id' => $warehouse->branch_id,
                    'warehouse_id' => $warehouse->id,
                ]),
                'status' => 'draft',
                'reason' => $payload['reason'] ?? null,
                'requested_by' => $payload['requested_by'] ?? null,
            ]);

            foreach ($items as $item) {
                $delta = (float) $item['quantity_delta'];

                if ($delta === 0.0) {
                    throw new InvalidArgumentException('Adjustment item delta cannot be zero.');
                }

                StockAdjustmentItem::query()->create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'quantity_delta' => $delta,
                    'note' => $item['note'] ?? null,
                ]);
            }

            return $adjustment->refresh()->load('items');
        });
    }

    public function approveAndPost(StockAdjustment $adjustment, ?int $approvedBy = null): StockAdjustment
    {
        if ($adjustment->status !== 'draft') {
            throw new InvalidArgumentException('Only draft stock adjustments may be approved.');
        }

        return DB::transaction(function () use ($adjustment, $approvedBy): StockAdjustment {
            $adjustment->loadMissing('items');

            foreach ($adjustment->items as $item) {
                $delta = (float) $item->quantity_delta;

                $this->stockPostingService->post([
                    'brand_id' => $adjustment->brand_id,
                    'city_id' => $adjustment->city_id,
                    'branch_id' => $adjustment->branch_id,
                    'warehouse_id' => $adjustment->warehouse_id,
                    'product_id' => $item->product_id,
                    'movement_type' => $delta > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($delta),
                    'source_type' => StockAdjustment::class,
                    'source_id' => $adjustment->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'adjustment_number' => $adjustment->adjustment_number,
                        'reason' => $adjustment->reason,
                        'item_note' => $item->note,
                    ],
                ]);
            }

            $adjustment->forceFill([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ])->save();

            return $adjustment->refresh()->load('items');
        });
    }

    /**
     * @param array<int, mixed> $productIds
     */
    private function assertProductsBelongToWarehouseBrand(array $productIds, int $brandId): void
    {
        $productBrandById = Product::query()
            ->whereIn('id', $productIds)
            ->pluck('brand_id', 'id');

        foreach ($productIds as $productId) {
            if ($productBrandById->get($productId) === null) {
                throw new InvalidArgumentException('Adjustment contains an unknown product.');
            }

            if ((int) $productBrandById->get($productId) !== $brandId) {
                throw new InvalidArgumentException('Adjustment product must belong to the destination warehouse brand.');
            }
        }
    }
}
