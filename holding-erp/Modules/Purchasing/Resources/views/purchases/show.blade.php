@extends('layouts.app')

@section('title', 'Purchase '.$purchase->po_number)

@section('content')
    <header>
        <p class="erp-kicker">Purchase Order</p>
        <h1>{{ $purchase->po_number }}</h1>
        <p>{{ $purchase->supplier?->name }} &middot; <span class="erp-chip">{{ $purchase->status }}</span></p>
    </header>

    <section class="erp-table-card" style="margin: 2rem 0;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Received</th>
                    <th>Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product?->name }}</td>
                        <td>{{ number_format((float) $item->quantity, 2) }}</td>
                        <td>Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ number_format((float) $item->received_quantity, 2) }}</td>
                        <td>Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    @can('approve', $purchase)
        <form method="POST" action="{{ route('purchasing.purchases.approve', $purchase) }}" style="margin-bottom: 1rem;">
            @csrf
            <button type="submit" class="erp-button">Approve purchase</button>
        </form>
    @endcan

    @can('receive', $purchase)
        <form method="POST" action="{{ route('purchasing.purchases.receive', $purchase) }}">
            @csrf
            <button type="submit" class="erp-button">Terima barang & posting stok</button>
        </form>
    @endcan
    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
