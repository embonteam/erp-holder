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
use Modules\Inventory\Http\Requests\StoreStockTransferRequest;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Services\StockTransferService;

class StockTransferController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', StockTransfer::class);

        return view('inventory::transfers.index', [
            'transfers' => StockTransfer::query()
                ->with(['sourceWarehouse', 'destinationWarehouse', 'items.product', 'requestedBy', 'approvedBy'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', StockTransfer::class);

        return view('inventory::transfers.create', [
            'warehouses' => Warehouse::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(
        StoreStockTransferRequest $request,
        StockTransferService $stockTransferService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', StockTransfer::class);

        $transfer = $stockTransferService->createDraft([
            ...$request->validated(),
            'requested_by' => $request->user()?->id,
        ]);

        $activityLogger->log(
            'inventory.transfer.created',
            $request->user(),
            $transfer,
            metadata: [
                'transfer_number' => $transfer->transfer_number,
                'items_count' => $transfer->items()->count(),
            ],
            newValues: $transfer->only(['transfer_number', 'status', 'source_warehouse_id', 'destination_warehouse_id']),
            request: $request,
        );

        return redirect()
            ->route('inventory.transfers.show', $transfer)
            ->with('status', 'Stock transfer draft berhasil dibuat.');
    }

    public function show(StockTransfer $transfer): View
    {
        Gate::authorize('view', $transfer);

        return view('inventory::transfers.show', [
            'transfer' => $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'items.product', 'requestedBy', 'approvedBy']),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(StockTransfer::class, $transfer->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function approve(
        Request $request,
        StockTransfer $transfer,
        StockTransferService $stockTransferService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('approve', $transfer);

        $oldValues = $transfer->only(['status', 'approved_by', 'approved_at']);
        $transfer = $stockTransferService->approve($transfer, $request->user()?->id);

        $activityLogger->log(
            'inventory.transfer.approved',
            $request->user(),
            $transfer,
            metadata: ['transfer_number' => $transfer->transfer_number],
            oldValues: $oldValues,
            newValues: $transfer->only(['status', 'approved_by', 'approved_at']),
            request: $request,
        );

        return redirect()
            ->route('inventory.transfers.show', $transfer)
            ->with('status', 'Stock transfer disetujui.');
    }

    public function dispatch(
        Request $request,
        StockTransfer $transfer,
        StockTransferService $stockTransferService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('dispatch', $transfer);

        $oldValues = $transfer->only(['status', 'dispatched_at']);
        $transfer = $stockTransferService->dispatch($transfer);

        $activityLogger->log(
            'inventory.transfer.dispatched',
            $request->user(),
            $transfer,
            metadata: [
                'transfer_number' => $transfer->transfer_number,
                'source_warehouse_id' => $transfer->source_warehouse_id,
            ],
            oldValues: $oldValues,
            newValues: $transfer->only(['status', 'dispatched_at']),
            request: $request,
        );

        return redirect()
            ->route('inventory.transfers.show', $transfer)
            ->with('status', 'Stock transfer sudah dispatched dan stock source sudah berkurang.');
    }

    public function receive(
        Request $request,
        StockTransfer $transfer,
        StockTransferService $stockTransferService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('receive', $transfer);

        $oldValues = $transfer->only(['status', 'received_at']);
        $transfer = $stockTransferService->receive($transfer);

        $activityLogger->log(
            'inventory.transfer.received',
            $request->user(),
            $transfer,
            metadata: [
                'transfer_number' => $transfer->transfer_number,
                'destination_warehouse_id' => $transfer->destination_warehouse_id,
            ],
            oldValues: $oldValues,
            newValues: $transfer->only(['status', 'received_at']),
            request: $request,
        );

        return redirect()
            ->route('inventory.transfers.show', $transfer)
            ->with('status', 'Stock transfer sudah received dan stock destination sudah bertambah.');
    }
}
