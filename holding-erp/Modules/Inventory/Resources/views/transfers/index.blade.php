@extends('layouts.app')

@section('title', 'Warehouse Transfers')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Control</p>
        <h1>Warehouse Transfers</h1>
        <p>Transfer stok antar warehouse dengan approval, dispatch, receive, dan stock ledger dua sisi.</p>
    </header>

    @can('create', \Modules\Inventory\Models\StockTransfer::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('inventory.transfers.create') }}">Buat transfer</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Requested</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->transfer_number }}</td>
                        <td>{{ $transfer->sourceWarehouse?->name }}</td>
                        <td>{{ $transfer->destinationWarehouse?->name }}</td>
                        <td><span class="erp-chip">{{ $transfer->status }}</span></td>
                        <td>{{ $transfer->items->count() }}</td>
                        <td>{{ $transfer->requestedBy?->name ?? '-' }}</td>
                        <td><a class="erp-link" href="{{ route('inventory.transfers.show', $transfer) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada warehouse transfer.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
