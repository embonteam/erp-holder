@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <header>
        <p class="erp-kicker">Purchasing Master Data</p>
        <h1>Edit Supplier</h1>
        <p>{{ $supplier->code }} · {{ $supplier->name }}</p>
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

    <form method="POST" action="{{ route('purchasing.suppliers.update', $supplier) }}" class="erp-form-card" style="margin-top: 2rem;">
        @csrf
        @method('PUT')

        <label class="erp-field">
            <span>Kode supplier</span>
            <input type="text" name="code" value="{{ old('code', $supplier->code) }}" required>
        </label>

        <label class="erp-field">
            <span>Nama supplier</span>
            <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required>
        </label>

        <label class="erp-field">
            <span>NPWP / Tax ID</span>
            <input type="text" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}">
        </label>

        <label class="erp-field">
            <span>Telepon</span>
            <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}">
        </label>

        <label class="erp-field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $supplier->email) }}">
        </label>

        <label class="erp-field">
            <span>Alamat</span>
            <textarea name="address" rows="4">{{ old('address', $supplier->address) }}</textarea>
        </label>

        <div class="erp-inline-actions">
            <button type="submit" class="erp-button">Update supplier</button>
            <a class="erp-link" href="{{ route('purchasing.suppliers.show', $supplier) }}">Batal</a>
        </div>
    </form>
@endsection
