@extends('layouts.app')

@section('title', 'Purchasing')

@section('content')
    <header>
        <p class="erp-kicker">Purchasing & Supplier</p>
        <h1>Purchase Orders</h1>
        <p>Approval-ready purchase flow feeding the warehouse ledger.</p>
    </header>

    @can('create', \Modules\Purchasing\Models\Purchase::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('purchasing.purchases.create') }}">Buat purchase order</a>
            <a class="erp-link" style="margin-left: 1rem;" href="{{ route('purchasing.suppliers.index') }}">Kelola supplier</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>PO</th>
                    <th>Supplier</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Items</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->po_number }}</td>
                        <td>{{ $purchase->supplier?->name }}</td>
                        <td><span class="erp-chip">{{ $purchase->status }}</span></td>
                        <td>Rp{{ number_format((float) $purchase->total_amount, 0, ',', '.') }}</td>
                        <td>{{ $purchase->items->count() }}</td>
                        <td>
                            <a class="erp-link" href="{{ route('purchasing.purchases.show', $purchase) }}">
                                Buka
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada purchase order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection


