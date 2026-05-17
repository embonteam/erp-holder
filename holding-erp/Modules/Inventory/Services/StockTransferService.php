<?php

namespace Modules\Inventory\Services;

use App\Core\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\StockTransferItem;

class StockTransferService
{
    public function __construct(
        private readonly StockPostingService $stockPostingService,
        private readonly DocumentNumberService $documentNumberService,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function createDraft(array $payload): StockTransfer
    {
        return DB::transaction(function () use ($payload): StockTransfer {
            $sourceWarehouse = Warehouse::query()->findOrFail($payload['source_warehouse_id']);
            $destinationWarehouse = Warehouse::query()->findOrFail($payload['destination_warehouse_id']);

            $this->assertWarehousesCanTransfer($sourceWarehouse, $destinationWarehouse);

            $items = collect($payload['items']);
            $this->assertProductsBelongToBrand($items->pluck('product_id')->all(), $sourceWarehouse->brand_id);

            $transfer = StockTransfer::query()->create([
                'brand_id' => $sourceWarehouse->brand_id,
                'city_id' => $sourceWarehouse->city_id,
                'branch_id' => $sourceWarehouse->branch_id,
                'source_warehouse_id' => $sourceWarehouse->id,
                'destination_warehouse_id' => $destinationWarehouse->id,
                'transfer_number' => $payload['transfer_number'] ?? $this->documentNumberService->next('TRF', [
                    'brand_id' => $sourceWarehouse->brand_id,
                    'city_id' => $sourceWarehouse->city_id,
                    'branch_id' => $sourceWarehouse->branch_id,
                    'warehouse_id' => $sourceWarehouse->id,
                ]),
                'status' => 'draft',
                'requested_by' => $payload['requested_by'] ?? null,
            ]);

            foreach ($items as $item) {
                StockTransferItem::query()->create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'requested_quantity' => (float) $item['requested_quantity'],
                    'dispatched_quantity' => 0,
                    'received_quantity' => 0,
                ]);
            }

            return $transfer->refresh()->load('items');
        });
    }

    public function approve(StockTransfer $transfer, ?int $approvedBy = null): StockTransfer
    {
        if ($transfer->status !== 'draft') {
            throw new InvalidArgumentException('Only draft stock transfers may be approved.');
        }

        $transfer->forceFill([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ])->save();

        return $transfer->refresh()->load('items');
    }

    public function dispatch(StockTransfer $transfer): StockTransfer
    {
        if ($transfer->status !== 'approved') {
            throw new InvalidArgumentException('Only approved stock transfers may be dispatched.');
        }

        return DB::transaction(function () use ($transfer): StockTransfer {
            $transfer->loadMissing(['items', 'sourceWarehouse']);
            $sourceWarehouse = $transfer->sourceWarehouse;

            foreach ($transfer->items as $item) {
                $quantity = (float) $item->requested_quantity;

                $this->stockPostingService->post([
                    'brand_id' => $sourceWarehouse->brand_id,
                    'city_id' => $sourceWarehouse->city_id,
                    'branch_id' => $sourceWarehouse->branch_id,
                    'warehouse_id' => $sourceWarehouse->id,
                    'product_id' => $item->product_id,
                    'movement_type' => 'transfer_out',
                    'quantity' => $quantity,
                    'source_type' => StockTransfer::class,
                    'source_id' => $transfer->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'transfer_number' => $transfer->transfer_number,
                        'destination_warehouse_id' => $transfer->destination_warehouse_id,
                    ],
                ]);

                $item->forceFill(['dispatched_quantity' => $quantity])->save();
            }

            $transfer->forceFill([
                'status' => 'dispatched',
                'dispatched_at' => now(),
            ])->save();

            return $transfer->refresh()->load('items');
        });
    }

    public function receive(StockTransfer $transfer): StockTransfer
    {
        if ($transfer->status !== 'dispatched') {
            throw new InvalidArgumentException('Only dispatched stock transfers may be received.');
        }

        return DB::transaction(function () use ($transfer): StockTransfer {
            $transfer->loadMissing(['items', 'destinationWarehouse']);
            $destinationWarehouse = $transfer->destinationWarehouse;

            foreach ($transfer->items as $item) {
                $quantity = (float) $item->dispatched_quantity;

                if ($quantity <= 0) {
                    continue;
                }

                $this->stockPostingService->post([
                    'brand_id' => $destinationWarehouse->brand_id,
                    'city_id' => $destinationWarehouse->city_id,
                    'branch_id' => $destinationWarehouse->branch_id,
                    'warehouse_id' => $destinationWarehouse->id,
                    'product_id' => $item->product_id,
                    'movement_type' => 'transfer_in',
                    'quantity' => $quantity,
                    'source_type' => StockTransfer::class,
                    'source_id' => $transfer->id,
                    'occurred_at' => now(),
                    'metadata' => [
                        'transfer_number' => $transfer->transfer_number,
                        'source_warehouse_id' => $transfer->source_warehouse_id,
                    ],
                ]);

                $item->forceFill(['received_quantity' => $quantity])->save();
            }

            $transfer->forceFill([
                'status' => 'received',
                'received_at' => now(),
            ])->save();

            return $transfer->refresh()->load('items');
        });
    }

    private function assertWarehousesCanTransfer(Warehouse $sourceWarehouse, Warehouse $destinationWarehouse): void
    {
        if ($sourceWarehouse->id === $destinationWarehouse->id) {
            throw new InvalidArgumentException('Source and destination warehouse must be different.');
        }

        if ((int) $sourceWarehouse->brand_id !== (int) $destinationWarehouse->brand_id) {
            throw new InvalidArgumentException('Warehouse transfer must stay inside the same brand.');
        }
    }

    /**
     * @param array<int, mixed> $productIds
     */
    private function assertProductsBelongToBrand(array $productIds, int $brandId): void
    {
        $productBrandById = Product::query()
            ->whereIn('id', $productIds)
            ->pluck('brand_id', 'id');

        foreach ($productIds as $productId) {
            if ($productBrandById->get($productId) === null) {
                throw new InvalidArgumentException('Stock transfer contains an unknown product.');
            }

            if ((int) $productBrandById->get($productId) !== $brandId) {
                throw new InvalidArgumentException('Stock transfer product must belong to the source warehouse brand.');
            }
        }
    }
}
