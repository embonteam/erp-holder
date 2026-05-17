<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Notifications\Jobs\CreateLowStockNotifications;

class StockPostingService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function post(array $payload): StockMovement
    {
        return DB::transaction(function () use ($payload): StockMovement {
            $quantity = (float) $payload['quantity'];
            $movementType = (string) $payload['movement_type'];
            $signedQuantity = $this->signedQuantity($movementType, $quantity);

            $stock = WarehouseStock::query()
                ->where('warehouse_id', $payload['warehouse_id'])
                ->where('product_id', $payload['product_id'])
                ->lockForUpdate()
                ->first();

            if ($stock === null) {
                $stock = WarehouseStock::query()->create([
                    'brand_id' => $payload['brand_id'],
                    'city_id' => $payload['city_id'],
                    'branch_id' => $payload['branch_id'],
                    'warehouse_id' => $payload['warehouse_id'],
                    'product_id' => $payload['product_id'],
                    'on_hand' => 0,
                    'reserved' => 0,
                    'average_cost' => 0,
                    'reorder_level' => 0,
                ]);
            }

            $newOnHand = (float) $stock->on_hand + $signedQuantity;

            if ($newOnHand < 0) {
                throw new InvalidArgumentException('Stock cannot become negative.');
            }

            $averageCost = $this->averageCostAfterMovement(
                currentQuantity: (float) $stock->on_hand,
                currentAverageCost: (float) $stock->average_cost,
                signedQuantity: $signedQuantity,
                incomingUnitCost: isset($payload['unit_cost']) ? (float) $payload['unit_cost'] : null,
            );

            $stock->forceFill([
                'on_hand' => $newOnHand,
                'average_cost' => $averageCost,
            ])->save();

            if ((float) $stock->reorder_level > 0 && $newOnHand <= (float) $stock->reorder_level) {
                CreateLowStockNotifications::dispatch($stock->id)
                    ->onQueue('notifications')
                    ->afterCommit();
            }

            return StockMovement::query()->create([
                'brand_id' => $payload['brand_id'],
                'city_id' => $payload['city_id'],
                'branch_id' => $payload['branch_id'],
                'warehouse_id' => $payload['warehouse_id'],
                'product_id' => $payload['product_id'],
                'movement_type' => $movementType,
                'source_type' => $payload['source_type'] ?? null,
                'source_id' => $payload['source_id'] ?? null,
                'quantity' => $quantity,
                'unit_cost' => $payload['unit_cost'] ?? null,
                'occurred_at' => $payload['occurred_at'] ?? now(),
                'metadata' => $payload['metadata'] ?? null,
            ]);
        });
    }

    private function signedQuantity(string $movementType, float $quantity): float
    {
        return match ($movementType) {
            'purchase', 'return', 'stock_opname_in', 'transfer_in', 'production_output' => $quantity,
            'retail_sales', 'internal_distribution', 'external_distribution', 'expired', 'damaged',
            'transfer_out', 'production_consumption', 'stock_opname_out' => -$quantity,
            'adjustment_in' => $quantity,
            'adjustment_out' => -$quantity,
            default => throw new InvalidArgumentException("Unsupported movement type [{$movementType}]."),
        };
    }

    private function averageCostAfterMovement(
        float $currentQuantity,
        float $currentAverageCost,
        float $signedQuantity,
        ?float $incomingUnitCost,
    ): float {
        if ($signedQuantity <= 0 || $incomingUnitCost === null) {
            return $currentAverageCost;
        }

        $newQuantity = $currentQuantity + $signedQuantity;

        if ($newQuantity <= 0) {
            return $incomingUnitCost;
        }

        return (($currentQuantity * $currentAverageCost) + ($signedQuantity * $incomingUnitCost)) / $newQuantity;
    }
}
