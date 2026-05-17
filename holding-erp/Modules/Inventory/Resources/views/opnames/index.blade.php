@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Control</p>
        <h1>Stock Opname</h1>
        <p>Hasil hitung fisik dikunci sebagai draft, lalu variance diposting ke stock ledger setelah approval.</p>
    </header>

    @can('create', \Modules\Inventory\Models\StockOpname::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('inventory.opnames.create') }}">Buat opname</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Warehouse</th>
                    <th>Counted On</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($opnames as $opname)
                    <tr>
                        <td>{{ $opname->opname_number }}</td>
                        <td>{{ $opname->warehouse?->name }}</td>
                        <td>{{ $opname->counted_on?->format('d M Y') }}</td>
                        <td><span class="erp-chip">{{ $opname->status }}</span></td>
                        <td>{{ $opname->items->count() }}</td>
                        <td>{{ $opname->createdBy?->name ?? '-' }}</td>
                        <td><a class="erp-link" href="{{ route('inventory.opnames.show', $opname) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada stock opname.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
