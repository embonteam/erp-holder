@extends('layouts.app')

@section('title', 'Adjustment '.$adjustment->adjustment_number)

@section('content')
    <header>
        <p class="erp-kicker">Stock Adjustment</p>
        <h1>{{ $adjustment->adjustment_number }}</h1>
        <p>{{ $adjustment->warehouse?->name }} · <span class="erp-chip">{{ $adjustment->status }}</span></p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Requested By</p>
            <strong>{{ $adjustment->requestedBy?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Approved By</p>
            <strong>{{ $adjustment->approvedBy?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Approved At</p>
            <strong>{{ $adjustment->approved_at?->format('d M Y H:i') ?? '-' }}</strong>
        </article>
    </section>

    <section class="erp-card" style="margin-top: 1rem;">
        <p class="erp-kicker">Reason</p>
        <p>{{ $adjustment->reason ?: '-' }}</p>
    </section>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Delta Qty</th>
                    <th>Movement</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($adjustment->items as $item)
                    <tr>
                        <td>{{ $item->product?->name }}</td>
                        <td>{{ number_format((float) $item->quantity_delta, 2) }}</td>
                        <td><span class="erp-chip">{{ (float) $item->quantity_delta > 0 ? 'adjustment_in' : 'adjustment_out' }}</span></td>
                        <td>{{ $item->note ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    @can('approve', $adjustment)
        <form method="POST" action="{{ route('inventory.adjustments.approve', $adjustment) }}" style="margin-top: 1.5rem;">
            @csrf
            <button type="submit" class="erp-button">Approve & posting stock</button>
        </form>
    @endcan

    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
