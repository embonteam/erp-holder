<?php

namespace Modules\Purchasing\Services;

use App\Core\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Modules\Purchasing\Models\Supplier;
use Modules\Purchasing\Repositories\SupplierRepository;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepository $suppliers,
        private readonly DocumentNumberService $documentNumberService,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): Supplier
    {
        return DB::transaction(function () use ($payload): Supplier {
            return $this->suppliers->create([
                'holding_id' => $payload['holding_id'],
                'code' => ($payload['code'] ?? null) ?: $this->documentNumberService->next('SUP', []),
                'name' => $payload['name'],
                'tax_id' => $payload['tax_id'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'email' => $payload['email'] ?? null,
                'address' => $payload['address'] ?? null,
                'is_active' => $payload['is_active'] ?? true,
                'metadata' => $payload['metadata'] ?? null,
            ]);
        });
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(Supplier $supplier, array $payload): Supplier
    {
        return DB::transaction(function () use ($supplier, $payload): Supplier {
            $this->suppliers->update($supplier, [
                'code' => ($payload['code'] ?? null) ?: $supplier->code,
                'name' => $payload['name'],
                'tax_id' => $payload['tax_id'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'email' => $payload['email'] ?? null,
                'address' => $payload['address'] ?? null,
                'metadata' => $payload['metadata'] ?? null,
            ]);

            return $supplier->refresh();
        });
    }

    public function setActive(Supplier $supplier, bool $active): Supplier
    {
        $supplier->forceFill(['is_active' => $active])->save();

        return $supplier->refresh();
    }
}
