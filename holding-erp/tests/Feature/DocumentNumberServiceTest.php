<?php

namespace Tests\Feature;

use App\Core\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_numbers_increment_per_scope_and_day(): void
    {
        $service = app(DocumentNumberService::class);
        $scope = [
            'brand_id' => 1,
            'city_id' => 2,
            'branch_id' => 3,
            'warehouse_id' => 4,
        ];

        $first = $service->next('PO', $scope, now()->setDate(2026, 5, 17));
        $second = $service->next('PO', $scope, now()->setDate(2026, 5, 17));

        $this->assertSame('PO-20260517-000001', $first);
        $this->assertSame('PO-20260517-000002', $second);
    }
}
