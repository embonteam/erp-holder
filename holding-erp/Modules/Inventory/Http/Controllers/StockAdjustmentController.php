<?php

namespace Modules\Inventory\Http\Controllers;

use App\Core\Activity\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Models\ActivityLog;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Http\Requests\StoreStockAdjustmentRequest;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Services\StockAdjustmentService;

class StockAdjustmentController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', StockAdjustment::class);

        return view('inventory::adjustments.index', [
            'adjustments' => StockAdjustment::query()
                ->with(['warehouse', 'items.product', 'requestedBy', 'approvedBy'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', StockAdjustment::class);

        return view('inventory::adjustments.create', [
            'warehouses' => Warehouse::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(
        StoreStockAdjustmentRequest $request,
        StockAdjustmentService $stockAdjustmentService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', StockAdjustment::class);

        $adjustment = $stockAdjustmentService->createDraft([
            ...$request->validated(),
            'requested_by' => $request->user()?->id,
        ]);

        $activityLogger->log(
            'inventory.adjustment.created',
            $request->user(),
            $adjustment,
            metadata: [
                'adjustment_number' => $adjustment->adjustment_number,
                'items_count' => $adjustment->items()->count(),
            ],
            newValues: $adjustment->only(['adjustment_number', 'status', 'reason', 'warehouse_id']),
            request: $request,
        );

        return redirect()
            ->route('inventory.adjustments.show', $adjustment)
            ->with('status', 'Stock adjustment draft berhasil dibuat.');
    }

    public function show(StockAdjustment $adjustment): View
    {
        Gate::authorize('view', $adjustment);

        return view('inventory::adjustments.show', [
            'adjustment' => $adjustment->load(['warehouse', 'items.product', 'requestedBy', 'approvedBy']),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(StockAdjustment::class, $adjustment->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function approve(
        Request $request,
        StockAdjustment $adjustment,
        StockAdjustmentService $stockAdjustmentService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('approve', $adjustment);

        $oldValues = $adjustment->only(['status', 'approved_by', 'approved_at']);
        $adjustment = $stockAdjustmentService->approveAndPost($adjustment, $request->user()?->id);

        $activityLogger->log(
            'inventory.adjustment.approved',
            $request->user(),
            $adjustment,
            metadata: [
                'adjustment_number' => $adjustment->adjustment_number,
                'warehouse_id' => $adjustment->warehouse_id,
                'items_count' => $adjustment->items()->count(),
            ],
            oldValues: $oldValues,
            newValues: $adjustment->only(['status', 'approved_by', 'approved_at']),
            request: $request,
        );

        return redirect()
            ->route('inventory.adjustments.show', $adjustment)
            ->with('status', 'Stock adjustment disetujui dan sudah diposting ke stock ledger.');
    }
}
