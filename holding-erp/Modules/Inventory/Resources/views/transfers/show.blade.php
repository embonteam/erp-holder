@extends('layouts.app')

@section('title', 'Transfer '.$transfer->transfer_number)

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Transfer</p>
        <h1>{{ $transfer->transfer_number }}</h1>
        <p>{{ $transfer->sourceWarehouse?->name }} → {{ $transfer->destinationWarehouse?->name }} · <span class="erp-chip">{{ $transfer->status }}</span></p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Requested By</p>
            <strong>{{ $transfer->requestedBy?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Approved By</p>
            <strong>{{ $transfer->approvedBy?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Timeline</p>
            <strong>
                Approved {{ $transfer->approved_at?->format('d M H:i') ?? '-' }}
                · Dispatch {{ $transfer->dispatched_at?->format('d M H:i') ?? '-' }}
                · Receive {{ $transfer->received_at?->format('d M H:i') ?? '-' }}
            </strong>
        </article>
    </section>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Requested</th>
                    <th>Dispatched</th>
                    <th>Received</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transfer->items as $item)
                    <tr>
                        <td>{{ $item->product?->name }}</td>
                        <td>{{ number_format((float) $item->requested_quantity, 2) }}</td>
                        <td>{{ number_format((float) $item->dispatched_quantity, 2) }}</td>
                        <td>{{ number_format((float) $item->received_quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <div class="erp-inline-actions" style="margin-top: 1.5rem;">
        @can('approve', $transfer)
            <form method="POST" action="{{ route('inventory.transfers.approve', $transfer) }}">
                @csrf
                <button type="submit" class="erp-button">Approve transfer</button>
            </form>
        @endcan

        @can('dispatch', $transfer)
            <form method="POST" action="{{ route('inventory.transfers.dispatch', $transfer) }}">
                @csrf
                <button type="submit" class="erp-button">Dispatch stock</button>
            </form>
        @endcan

        @can('receive', $transfer)
            <form method="POST" action="{{ route('inventory.transfers.receive', $transfer) }}">
                @csrf
                <button type="submit" class="erp-button">Receive stock</button>
            </form>
        @endcan
    </div>

    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
