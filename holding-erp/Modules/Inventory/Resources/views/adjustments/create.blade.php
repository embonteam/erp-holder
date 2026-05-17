@extends('layouts.app')

@section('title', 'Buat Stock Adjustment')

@section('content')
    <header>
        <p class="erp-kicker">Warehouse Control</p>
        <h1>Buat Stock Adjustment</h1>
        <p>Gunakan angka positif untuk menambah stok, negatif untuk mengurangi stok.</p>
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

    <form method="POST" action="{{ route('inventory.adjustments.store') }}" class="erp-form-card" style="margin-top: 2rem;">
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
            <span>Reason</span>
            <textarea name="reason" rows="3" placeholder="Contoh: koreksi damaged stock / selisih fisik">{{ old('reason') }}</textarea>
        </label>

        <div class="erp-table-card">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Delta Qty</th>
                        <th>Note</th>
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
                        <td><input type="number" step="0.01" name="items[0][quantity_delta]" value="{{ old('items.0.quantity_delta', 1) }}" required></td>
                        <td><input type="text" name="items[0][note]" value="{{ old('items.0.note') }}"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="erp-inline-actions">
            <button type="submit" class="erp-button">Simpan draft adjustment</button>
            <a class="erp-link" href="{{ route('inventory.adjustments.index') }}">Kembali</a>
        </div>
    </form>
@endsection
