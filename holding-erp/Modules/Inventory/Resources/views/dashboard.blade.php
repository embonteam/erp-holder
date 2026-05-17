@extends('layouts.app')

@section('title', 'Warehouse & Inventory')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse & Inventory</p>
        <h1>Inventory Dashboard</h1>
        <p>Ledger-driven stock monitoring across scoped warehouses.</p>
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('inventory.adjustments.index') }}">Stock adjustments</a>
            <a class="erp-button erp-button-link" href="{{ route('inventory.opnames.index') }}">Stock opname</a>
            <a class="erp-button erp-button-link" href="{{ route('inventory.transfers.index') }}">Warehouse transfers</a>
        </p>
    </header>

    <section class="erp-panel-grid" style="margin: 2rem 0;">
        <article class="erp-card">
            <p class="erp-kicker">Tracked stock rows</p>
            <strong class="erp-metric">{{ $stockCount }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Low stock</p>
            <strong class="erp-metric" data-tone="danger">{{ $lowStockCount }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Stock movements</p>
            <strong class="erp-metric">{{ $movementCount }}</strong>
        </article>
    </section>

    <section class="erp-table-card">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Occurred</th>
                    <th>Product</th>
                    <th>Warehouse</th>
                    <th>Type</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentMovements as $movement)
                    <tr>
                        <td>{{ $movement->occurred_at?->format('d M Y H:i') }}</td>
                        <td>{{ $movement->product?->name }}</td>
                        <td>{{ $movement->warehouse?->name }}</td>
                        <td><span class="erp-chip">{{ $movement->movement_type }}</span></td>
                        <td>{{ number_format((float) $movement->quantity, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada pergerakan stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection


