<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\WarehouseStock;

class InventoryDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('inventory::dashboard', [
            'stockCount' => WarehouseStock::query()->count(),
            'lowStockCount' => WarehouseStock::query()
                ->whereColumn('on_hand', '<=', 'reorder_level')
                ->count(),
            'movementCount' => StockMovement::query()->count(),
            'recentMovements' => StockMovement::query()
                ->with(['product', 'warehouse'])
                ->latest('occurred_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
