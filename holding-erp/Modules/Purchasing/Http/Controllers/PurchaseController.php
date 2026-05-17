<?php

namespace Modules\Purchasing\Http\Controllers;

use App\Core\Activity\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Models\ActivityLog;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Purchasing\Http\Requests\StorePurchaseRequest;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\Supplier;
use Modules\Purchasing\Services\PurchaseService;

class PurchaseController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Purchase::class);

        return view('purchasing::purchases.index', [
            'purchases' => Purchase::query()
                ->with(['supplier', 'items.product'])
                ->latest()
                ->get(),
        ]);
    }

    public function show(Purchase $purchase): View
    {
        Gate::authorize('view', $purchase);

        return view('purchasing::purchases.show', [
            'purchase' => $purchase->load(['supplier', 'items.product']),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(Purchase::class, $purchase->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Purchase::class);

        return view('purchasing::purchases.create', [
            'suppliers' => Supplier::query()->where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(
        StorePurchaseRequest $request,
        PurchaseService $purchaseService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', Purchase::class);

        $purchase = $purchaseService->createDraft([
            ...$request->validated(),
            'requested_by' => $request->user()?->id,
        ]);

        $activityLogger->log(
            'purchasing.purchase.created',
            $request->user(),
            $purchase,
            metadata: [
                'po_number' => $purchase->po_number,
                'supplier_id' => $purchase->supplier_id,
                'items_count' => $purchase->items()->count(),
            ],
            newValues: $purchase->only(['po_number', 'status', 'subtotal', 'tax_amount', 'total_amount']),
            request: $request,
        );

        return redirect()
            ->route('purchasing.purchases.show', $purchase)
            ->with('status', 'Purchase draft berhasil dibuat.');
    }

    public function approve(
        Request $request,
        Purchase $purchase,
        PurchaseService $purchaseService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('approve', $purchase);

        $oldValues = $purchase->only(['status', 'approved_by', 'approved_at']);
        $purchaseService->approve($purchase, $request->user()?->id);
        $purchase->refresh();

        $activityLogger->log(
            'purchasing.purchase.approved',
            $request->user(),
            $purchase,
            metadata: ['po_number' => $purchase->po_number],
            oldValues: $oldValues,
            newValues: $purchase->only(['status', 'approved_by', 'approved_at']),
            request: $request,
        );

        return redirect()
            ->route('purchasing.purchases.show', $purchase)
            ->with('status', 'Purchase berhasil di-approve.');
    }

    public function receive(
        Request $request,
        Purchase $purchase,
        PurchaseService $purchaseService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('receive', $purchase);

        $oldValues = $purchase->only(['status']);
        $purchaseService->receive($purchase);
        $purchase->refresh();

        $activityLogger->log(
            'purchasing.purchase.received',
            $request->user(),
            $purchase,
            metadata: [
                'po_number' => $purchase->po_number,
                'warehouse_id' => $purchase->warehouse_id,
                'items_count' => $purchase->items()->count(),
            ],
            oldValues: $oldValues,
            newValues: $purchase->only(['status']),
            request: $request,
        );

        return redirect()
            ->route('purchasing.purchases.show', $purchase)
            ->with('status', 'Purchase berhasil diterima dan stok sudah diposting.');
    }
}
