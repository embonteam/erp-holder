<?php

namespace Modules\Inventory\Services;

use App\Core\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockOpname;
use Modules\Inventory\Models\StockOpnameItem;
use Modules\Inventory\Models\WarehouseStock;

class StockOpnameService
{
    public function __construct(
        private readonly StockPostingService $stockPostingService,
        private readonly DocumentNumberService $documentNumberService,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function createDraft(array $payload): StockOpname
    {
        return DB::transaction(function () use ($payload): StockOpname {
            $warehouse = Warehouse::query()->findOrFail($payload['warehouse_id']);
            $items = collect($payload['items']);
            $productIds = $items->pluck('product_id')->all();

            $this->assertProductsBelongToWarehouseBrand($productIds, $warehouse->brand_id);

            $systemQuantityByProduct = WarehouseStock::query()
                ->where('warehouse_id', $warehouse->id)
                ->whereIn('product_id', $productIds)
                ->pluck('on_hand', 'product_id');

            $opname = StockOpname::query()->create([
                'brand_id' => $warehouse->brand_id,
                'city_id' => $warehouse->city_id,
                'branch_id' => $warehouse->branch_id,
                'warehouse_id' => $warehouse->id,
                'opname_number' => $payload['opname_number'] ?? $this->documentNumberService->next('OPN', [
                    'brand_id' => $warehouse->brand_id,
                    'city_id' => $warehouse->city_id,
                    'branch_id' => $warehouse->branch_id,
                    'warehouse_id' => $warehouse->id,
                ]),
                'status' => 'draft',
                'counted_on' => $payload['counted_on'],
                'created_by' => $payload['created_by'] ?? null,
            ]);

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $systemQuantity = (float) ($systemQuantityByProduct->get($productId) ?? 0);
                $countedQuantity = (float) $item['counted_quantity'];

                StockOpnameItem::query()->create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $productId,
                    'system_quantity' => $systemQuantity,
                    'counted_quantity' => $countedQuantity,
                    'variance_quantity' => $countedQuantity - $systemQuantity,
                ]);
            }

            return $opname->refresh()->load('items');
        });
    }

    public function approveAndPost(StockOpname $opname, ?int $approvedBy = null): StockOpname
    {
        if ($opname->status !== 'draft') {
            throw new InvalidArgumentException('Only draft stock opnames may be approved.');
        }

        return DB::transaction(function () use ($opname, $approvedBy): StockOpname {
            $opname->loadMissing('items');

            foreach ($opname->items as $item) {
                $variance = (float) $item->variance_quantity;

                if ($variance === 0.0) {
                    continue;
                }

                $this->stockPostingService->post([
                    'brand_id' => $opname->brand_id,
                    'city_id' => $opname->city_id,
                    'branch_id' => $opname->branch_id,
                    'warehouse_id' => $opname->warehouse_id,
                    'product_id' => $item->product_id,
                    'movement_type' => $variance > 0 ? 'stock_opname_in' : 'stock_opname_out',
                    'quantity' => abs($variance),
                    'source_type' => StockOpname::class,
                    'source_id' => $opname->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'opname_number' => $opname->opname_number,
                        'counted_on' => $opname->counted_on?->toDateString(),
                        'system_quantity' => (float) $item->system_quantity,
                        'counted_quantity' => (float) $item->counted_quantity,
                    ],
                ]);
            }

            $opname->forceFill([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ])->save();

            return $opname->refresh()->load('items');
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
                throw new InvalidArgumentException('Stock opname contains an unknown product.');
            }

            if ((int) $productBrandById->get($productId) !== $brandId) {
                throw new InvalidArgumentException('Stock opname product must belong to the counted warehouse brand.');
            }
        }
    }
}
