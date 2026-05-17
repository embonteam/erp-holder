<?php

namespace Modules\Purchasing\Http\Controllers;

use App\Core\Activity\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Audit\Models\ActivityLog;
use Modules\Holding\Models\Holding;
use Modules\Purchasing\Http\Requests\StoreSupplierRequest;
use Modules\Purchasing\Http\Requests\UpdateSupplierRequest;
use Modules\Purchasing\Models\Supplier;
use Modules\Purchasing\Services\SupplierService;

class SupplierController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Supplier::class);

        return view('purchasing::suppliers.index', [
            'suppliers' => Supplier::query()
                ->withCount('purchases')
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Supplier $supplier): View
    {
        Gate::authorize('view', $supplier);

        return view('purchasing::suppliers.show', [
            'supplier' => $supplier->load(['purchases' => fn ($query) => $query->latest()->limit(10)]),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(Supplier::class, $supplier->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Supplier::class);

        return view('purchasing::suppliers.create');
    }

    public function store(
        StoreSupplierRequest $request,
        SupplierService $supplierService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', Supplier::class);

        $supplier = $supplierService->create([
            ...$request->validated(),
            'holding_id' => $this->holdingIdFor($request),
        ]);

        $activityLogger->log(
            'purchasing.supplier.created',
            $request->user(),
            $supplier,
            metadata: ['supplier_code' => $supplier->code],
            newValues: $supplier->only(['holding_id', 'code', 'name', 'tax_id', 'phone', 'email', 'address', 'is_active']),
            request: $request,
        );

        return redirect()
            ->route('purchasing.suppliers.show', $supplier)
            ->with('status', 'Supplier berhasil dibuat dan siap dipakai untuk purchase order.');
    }

    public function edit(Supplier $supplier): View
    {
        Gate::authorize('update', $supplier);

        return view('purchasing::suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(
        UpdateSupplierRequest $request,
        Supplier $supplier,
        SupplierService $supplierService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('update', $supplier);

        $oldValues = $supplier->only(['code', 'name', 'tax_id', 'phone', 'email', 'address', 'is_active']);
        $supplier = $supplierService->update($supplier, $request->validated());

        $activityLogger->log(
            'purchasing.supplier.updated',
            $request->user(),
            $supplier,
            metadata: ['supplier_code' => $supplier->code],
            oldValues: $oldValues,
            newValues: $supplier->only(['code', 'name', 'tax_id', 'phone', 'email', 'address', 'is_active']),
            request: $request,
        );

        return redirect()
            ->route('purchasing.suppliers.show', $supplier)
            ->with('status', 'Supplier berhasil diperbarui.');
    }

    public function deactivate(
        Request $request,
        Supplier $supplier,
        SupplierService $supplierService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('deactivate', $supplier);

        $oldValues = $supplier->only(['is_active']);
        $supplier = $supplierService->setActive($supplier, false);

        $activityLogger->log(
            'purchasing.supplier.deactivated',
            $request->user(),
            $supplier,
            metadata: ['supplier_code' => $supplier->code],
            oldValues: $oldValues,
            newValues: ['is_active' => false],
            request: $request,
        );

        return redirect()
            ->route('purchasing.suppliers.show', $supplier)
            ->with('status', 'Supplier dinonaktifkan. PO baru tidak akan menampilkan supplier ini.');
    }

    public function reactivate(
        Request $request,
        Supplier $supplier,
        SupplierService $supplierService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('reactivate', $supplier);

        $oldValues = $supplier->only(['is_active']);
        $supplier = $supplierService->setActive($supplier, true);

        $activityLogger->log(
            'purchasing.supplier.reactivated',
            $request->user(),
            $supplier,
            metadata: ['supplier_code' => $supplier->code],
            oldValues: $oldValues,
            newValues: ['is_active' => true],
            request: $request,
        );

        return redirect()
            ->route('purchasing.suppliers.show', $supplier)
            ->with('status', 'Supplier diaktifkan kembali.');
    }

    private function holdingIdFor(Request $request): int
    {
        $holdingId = $request->user()?->holding_id ?? Holding::query()->value('id');

        if ($holdingId === null) {
            throw ValidationException::withMessages([
                'holding_id' => 'Holding belum tersedia. Jalankan master data holding terlebih dahulu.',
            ]);
        }

        return (int) $holdingId;
    }
}
