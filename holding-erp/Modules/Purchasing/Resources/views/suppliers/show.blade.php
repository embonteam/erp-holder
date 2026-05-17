@extends('layouts.app')

@section('title', 'Supplier '.$supplier->code)

@section('content')
    <header>
        <p class="erp-kicker">Supplier Detail</p>
        <h1>{{ $supplier->name }}</h1>
        <p>{{ $supplier->code }} · <span class="erp-chip">{{ $supplier->is_active ? 'active' : 'inactive' }}</span></p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Tax ID</p>
            <strong>{{ $supplier->tax_id ?: '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Telepon</p>
            <strong>{{ $supplier->phone ?: '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Email</p>
            <strong>{{ $supplier->email ?: '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Total PO</p>
            <strong>{{ $supplier->purchases()->count() }}</strong>
        </article>
    </section>

    <section class="erp-card" style="margin-top: 1rem;">
        <p class="erp-kicker">Alamat</p>
        <p>{{ $supplier->address ?: '-' }}</p>
    </section>

    <div class="erp-inline-actions" style="margin-top: 1.5rem;">
        @can('update', $supplier)
            <a class="erp-button erp-button-link" href="{{ route('purchasing.suppliers.edit', $supplier) }}">Edit supplier</a>
        @endcan

        @if ($supplier->is_active)
            @can('deactivate', $supplier)
                <form method="POST" action="{{ route('purchasing.suppliers.deactivate', $supplier) }}">
                    @csrf
                    <button type="submit" class="erp-button" style="background: var(--erp-danger);">Nonaktifkan</button>
                </form>
            @endcan
        @else
            @can('reactivate', $supplier)
                <form method="POST" action="{{ route('purchasing.suppliers.reactivate', $supplier) }}">
                    @csrf
                    <button type="submit" class="erp-button">Aktifkan kembali</button>
                </form>
            @endcan
        @endif

        <a class="erp-link" href="{{ route('purchasing.suppliers.index') }}">Kembali ke daftar</a>
    </div>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>PO terakhir</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($supplier->purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->po_number }}</td>
                        <td><span class="erp-chip">{{ $purchase->status }}</span></td>
                        <td>Rp{{ number_format((float) $purchase->total_amount, 0, ',', '.') }}</td>
                        <td><a class="erp-link" href="{{ route('purchasing.purchases.show', $purchase) }}">Buka PO</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada purchase order untuk supplier ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
