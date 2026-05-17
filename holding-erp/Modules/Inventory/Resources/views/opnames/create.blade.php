@extends('layouts.app')

@section('title', 'Buat Stock Opname')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Control</p>
        <h1>Buat Stock Opname</h1>
        <p>Masukkan hasil hitung fisik. Sistem akan menyimpan system quantity saat draft dibuat.</p>
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

    <form method="POST" action="{{ route('inventory.opnames.store') }}" class="erp-form-card" style="margin-top: 2rem;">
        @csrf

        <label class="erp-field">
            <span>Warehouse</span>
            <select name="warehouse_id" required>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((int) old('warehouse_id') === (int) $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>Counted On</span>
            <input type="date" name="counted_on" value="{{ old('counted_on', now()->toDateString()) }}" required>
        </label>

        <div class="erp-table-card">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Counted Qty</th>
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
                        <td><input type="number" step="0.01" min="0" name="items[0][counted_quantity]" value="{{ old('items.0.counted_quantity', 0) }}" required></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="erp-inline-actions">
            <button type="submit" class="erp-button">Simpan draft opname</button>
            <a class="erp-link" href="{{ route('inventory.opnames.index') }}">Kembali</a>
        </div>
    </form>
@endsection
