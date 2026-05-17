@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Control</p>
        <h1>Stock Adjustments</h1>
        <p>Perubahan stok manual wajib melalui approval dan diposting sebagai stock movement.</p>
    </header>

    @can('create', \Modules\Inventory\Models\StockAdjustment::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('inventory.adjustments.create') }}">Buat adjustment</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Warehouse</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Requested</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($adjustments as $adjustment)
                    <tr>
                        <td>{{ $adjustment->adjustment_number }}</td>
                        <td>{{ $adjustment->warehouse?->name }}</td>
                        <td><span class="erp-chip">{{ $adjustment->status }}</span></td>
                        <td>{{ $adjustment->items->count() }}</td>
                        <td>{{ $adjustment->requestedBy?->name ?? '-' }}</td>
                        <td><a class="erp-link" href="{{ route('inventory.adjustments.show', $adjustment) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada stock adjustment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
