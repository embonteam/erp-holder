<?php

namespace App\Core\Services;

use App\Core\Models\DocumentSequence;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * @param array<string, int|null> $scope
     */
    public function next(string $documentType, array $scope, ?CarbonInterface $date = null): string
    {
        $date ??= now();
        $dateKey = $date->format('Ymd');
        $scopeKey = $this->scopeKey($scope);

        return DB::transaction(function () use ($documentType, $dateKey, $scope, $scopeKey): string {
            DocumentSequence::query()->upsert([
                [
                    'document_type' => $documentType,
                    'date_key' => $dateKey,
                    'scope_key' => $scopeKey,
                    'brand_id' => $scope['brand_id'] ?? null,
                    'city_id' => $scope['city_id'] ?? null,
                    'branch_id' => $scope['branch_id'] ?? null,
                    'warehouse_id' => $scope['warehouse_id'] ?? null,
                    'current_value' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ], ['document_type', 'date_key', 'scope_key'], []);

            $sequence = DocumentSequence::query()
                ->where('document_type', $documentType)
                ->where('date_key', $dateKey)
                ->where('scope_key', $scopeKey)
                ->lockForUpdate()
                ->firstOrFail();

            $sequence->increment('current_value');

            return sprintf('%s-%s-%06d', strtoupper($documentType), $dateKey, $sequence->fresh()->current_value);
        });
    }

    /**
     * @param array<string, int|null> $scope
     */
    private function scopeKey(array $scope): string
    {
        return collect(['brand_id', 'city_id', 'branch_id', 'warehouse_id'])
            ->map(fn (string $key): string => $key.':'.($scope[$key] ?? 'global'))
            ->implode('|');
    }
}
