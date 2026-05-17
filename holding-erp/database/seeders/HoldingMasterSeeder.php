<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\HoldingCityPosition;
use Modules\Holding\Models\Warehouse;

class HoldingMasterSeeder extends Seeder
{
    public function run(): void
    {
        $holding = Holding::query()->updateOrCreate(
            ['code' => 'ICON-HOLDING'],
            ['name' => 'ICON Holding', 'is_active' => true],
        );

        $city = City::query()->updateOrCreate(
            ['code' => 'SMD'],
            ['holding_id' => $holding->id, 'name' => 'Samarinda', 'province' => 'Kalimantan Timur', 'is_active' => true],
        );

        HoldingCityPosition::query()->updateOrCreate(
            ['holding_id' => $holding->id, 'city_id' => $city->id],
            ['code' => 'SMD-REGION', 'name' => 'Samarinda Region', 'is_active' => true],
        );

        $brands = [
            ['code' => 'ICONMART', 'name' => 'ICONMART', 'business_type' => 'retail'],
            ['code' => 'VINZ', 'name' => 'Vinz Ice Cream', 'business_type' => 'fast_food'],
            ['code' => 'SATEMERAH', 'name' => 'Sate Merah', 'business_type' => 'qsr'],
            ['code' => 'SHALIMAR', 'name' => 'Shalimar Catering', 'business_type' => 'catering'],
        ];

        foreach ($brands as $brandPayload) {
            $brand = Brand::query()->updateOrCreate(
                ['code' => $brandPayload['code']],
                $brandPayload + ['holding_id' => $holding->id, 'is_active' => true],
            );

            $branch = Branch::query()->updateOrCreate(
                ['code' => $brandPayload['code'].'-SMD-001'],
                [
                    'holding_id' => $holding->id,
                    'brand_id' => $brand->id,
                    'city_id' => $city->id,
                    'name' => $brandPayload['name'].' Samarinda 001',
                    'branch_type' => $brandPayload['business_type'] === 'retail' ? 'store' : 'outlet',
                    'is_active' => true,
                ],
            );

            Warehouse::query()->updateOrCreate(
                ['code' => $brandPayload['code'].'-SMD-WH-001'],
                [
                    'holding_id' => $holding->id,
                    'brand_id' => $brand->id,
                    'city_id' => $city->id,
                    'branch_id' => $branch->id,
                    'name' => $brandPayload['name'].' Samarinda Warehouse',
                    'warehouse_type' => $brandPayload['business_type'] === 'retail' ? 'central' : 'branch',
                    'is_active' => true,
                ],
            );

            if ($brandPayload['code'] === 'ICONMART') {
                Warehouse::query()->updateOrCreate(
                    ['code' => 'ICONMART-SMD-WH-002'],
                    [
                        'holding_id' => $holding->id,
                        'brand_id' => $brand->id,
                        'city_id' => $city->id,
                        'branch_id' => $branch->id,
                        'name' => 'ICONMART Samarinda Secondary Warehouse',
                        'warehouse_type' => 'branch',
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
