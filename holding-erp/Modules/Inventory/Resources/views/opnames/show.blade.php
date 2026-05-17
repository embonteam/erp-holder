@extends('layouts.app')

@section('title', 'Opname '.$opname->opname_number)

@section('content')
    <header>
        <p class="erp-kicker">Stock Opname</p>
        <h1>{{ $opname->opname_number }}</h1>
        <p>{{ $opname->warehouse?->name }} · {{ $opname->counted_on?->format('d M Y') }} · <span class="erp-chip">{{ $opname->status }}</span></p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Created By</p>
            <strong>{{ $opname->createdBy?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Approved By</p>
            <strong>{{ $opname->approvedBy?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Approved At</p>
            <strong>{{ $opname->approved_at?->format('d M Y H:i') ?? '-' }}</strong>
        </article>
    </section>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>System Qty</th>
                    <th>Counted Qty</th>
                    <th>Variance</th>
                    <th>Movement</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($opname->items as $item)
                    <tr>
                        <td>{{ $item->product?->name }}</td>
                        <td>{{ number_format((float) $item->system_quantity, 2) }}</td>
                        <td>{{ number_format((float) $item->counted_quantity, 2) }}</td>
                        <td>{{ number_format((float) $item->variance_quantity, 2) }}</td>
                        <td>
                            @if ((float) $item->variance_quantity > 0)
                                <span class="erp-chip">stock_opname_in</span>
                            @elseif ((float) $item->variance_quantity < 0)
                                <span class="erp-chip">stock_opname_out</span>
                            @else
                                <span class="erp-chip">no_movement</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    @can('approve', $opname)
        <form method="POST" action="{{ route('inventory.opnames.approve', $opname) }}" style="margin-top: 1.5rem;">
            @csrf
            <button type="submit" class="erp-button">Approve & posting variance</button>
        </form>
    @endcan

    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
