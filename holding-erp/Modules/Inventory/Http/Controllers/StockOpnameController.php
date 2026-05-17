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
use Modules\Inventory\Http\Requests\StoreStockOpnameRequest;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockOpname;
use Modules\Inventory\Services\StockOpnameService;

class StockOpnameController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', StockOpname::class);

        return view('inventory::opnames.index', [
            'opnames' => StockOpname::query()
                ->with(['warehouse', 'items.product', 'createdBy', 'approvedBy'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', StockOpname::class);

        return view('inventory::opnames.create', [
            'warehouses' => Warehouse::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(
        StoreStockOpnameRequest $request,
        StockOpnameService $stockOpnameService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', StockOpname::class);

        $opname = $stockOpnameService->createDraft([
            ...$request->validated(),
            'created_by' => $request->user()?->id,
        ]);

        $activityLogger->log(
            'inventory.opname.created',
            $request->user(),
            $opname,
            metadata: [
                'opname_number' => $opname->opname_number,
                'items_count' => $opname->items()->count(),
            ],
            newValues: $opname->only(['opname_number', 'status', 'counted_on', 'warehouse_id']),
            request: $request,
        );

        return redirect()
            ->route('inventory.opnames.show', $opname)
            ->with('status', 'Stock opname draft berhasil dibuat.');
    }

    public function show(StockOpname $opname): View
    {
        Gate::authorize('view', $opname);

        return view('inventory::opnames.show', [
            'opname' => $opname->load(['warehouse', 'items.product', 'createdBy', 'approvedBy']),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(StockOpname::class, $opname->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function approve(
        Request $request,
        StockOpname $opname,
        StockOpnameService $stockOpnameService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('approve', $opname);

        $oldValues = $opname->only(['status', 'approved_by', 'approved_at']);
        $opname = $stockOpnameService->approveAndPost($opname, $request->user()?->id);

        $activityLogger->log(
            'inventory.opname.approved',
            $request->user(),
            $opname,
            metadata: [
                'opname_number' => $opname->opname_number,
                'warehouse_id' => $opname->warehouse_id,
                'items_count' => $opname->items()->count(),
            ],
            oldValues: $oldValues,
            newValues: $opname->only(['status', 'approved_by', 'approved_at']),
            request: $request,
        );

        return redirect()
            ->route('inventory.opnames.show', $opname)
            ->with('status', 'Stock opname disetujui dan variance sudah diposting ke stock ledger.');
    }
}
