@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <header>
        <p class="erp-kicker">Purchasing Master Data</p>
        <h1>Supplier Management</h1>
        <p>Master supplier holding untuk purchase order, invoice supplier, pajak pembelian, dan analitik performa vendor.</p>
    </header>

    @can('create', \Modules\Purchasing\Models\Supplier::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('purchasing.suppliers.create') }}">Tambah supplier</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Supplier</th>
                    <th>Kontak</th>
                    <th>Status</th>
                    <th>PO</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->code }}</td>
                        <td>
                            <strong>{{ $supplier->name }}</strong><br>
                            <span style="color: var(--erp-muted);">{{ $supplier->tax_id ?: 'NPWP belum diisi' }}</span>
                        </td>
                        <td>
                            {{ $supplier->phone ?: '-' }}<br>
                            <span style="color: var(--erp-muted);">{{ $supplier->email ?: '-' }}</span>
                        </td>
                        <td><span class="erp-chip">{{ $supplier->is_active ? 'active' : 'inactive' }}</span></td>
                        <td>{{ $supplier->purchases_count }}</td>
                        <td>
                            <a class="erp-link" href="{{ route('purchasing.suppliers.show', $supplier) }}">Buka</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
