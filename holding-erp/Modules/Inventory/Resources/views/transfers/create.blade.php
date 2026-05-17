@extends('layouts.app')

@section('title', 'Buat Warehouse Transfer')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Control</p>
        <h1>Buat Warehouse Transfer</h1>
        <p>Source dan destination harus berbeda dan berada dalam brand yang sama.</p>
    </header>

    @if ($errors->any())
        <div class="erp-alert" style="background: rgba(192, 57, 43, 0.1); color: var(--erp-danger); margin-top: 1rem;">
            <strong>Data belum valid.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.transfers.store') }}" class="erp-form-card" style="margin-top: 2rem;">
        @csrf

        <label class="erp-field">
            <span>Source Warehouse</span>
            <select name="source_warehouse_id" required>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((int) old('source_warehouse_id') === (int) $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>Destination Warehouse</span>
            <select name="destination_warehouse_id" required>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((int) old('destination_warehouse_id') === (int) $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </label>

        <div class="erp-table-card">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Requested Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][product_id]" required>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected((int) old('items.0.product_id') === (int) $product->id)>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.01" min="0.01" name="items[0][requested_quantity]" value="{{ old('items.0.requested_quantity', 1) }}" required></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="erp-inline-actions">
            <button type="submit" class="erp-button">Simpan draft transfer</button>
            <a class="erp-link" href="{{ route('inventory.transfers.index') }}">Kembali</a>
        </div>
    </form>
@endsection
