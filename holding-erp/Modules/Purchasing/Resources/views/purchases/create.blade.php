@extends('layouts.app')

@section('title', 'Create Purchase')

@section('content')
    <header>
        <p class="erp-kicker">Purchasing & Supplier</p>
        <h1>Buat Purchase Order</h1>
        <p>Draft baru akan menunggu approval sebelum barang dapat diterima.</p>
    </header>

    <form method="POST" action="{{ route('purchasing.purchases.store') }}" class="erp-form-card" style="margin-top: 2rem;">
        @csrf

        <label class="erp-field">
            <span>Supplier</span>
            <select name="supplier_id" required>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>Warehouse tujuan</span>
            <select name="warehouse_id" required>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </label>

        <div class="erp-table-card">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Tax %</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][product_id]" required>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.01" min="0.01" name="items[0][quantity]" value="1" required></td>
                        <td><input type="number" step="0.01" min="0" name="items[0][unit_price]" value="0" required></td>
                        <td><input type="number" step="0.01" min="0" name="items[0][tax_rate]" value="11"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="submit" class="erp-button">Simpan draft purchase</button>
    </form>
@endsection
