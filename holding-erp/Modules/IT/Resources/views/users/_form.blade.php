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

<form method="POST" action="{{ $action }}" class="erp-form-card" style="margin-top: 2rem;">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <section class="erp-panel-grid">
        <label class="erp-field">
            <span>Nama</span>
            <input type="text" name="name" value="{{ old('name', $managedUser->name) }}" required>
        </label>

        <label class="erp-field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $managedUser->email) }}" required>
        </label>

        <label class="erp-field">
            <span>Password {{ $managedUser->exists ? '(kosongkan jika tidak diganti)' : '' }}</span>
            <input type="password" name="password" {{ $managedUser->exists ? '' : 'required' }}>
        </label>

        <label class="erp-field">
            <span>Role</span>
            <select name="role_id" required>
                <option value="">Pilih role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((int) old('role_id', $managedUser->role_id) === (int) $role->id)>
                        {{ $role->name }} · {{ $role->scope_level }}
                    </option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="erp-panel-grid">
        <label class="erp-field">
            <span>Holding</span>
            <select name="holding_id" required>
                <option value="">Pilih holding</option>
                @foreach ($holdings as $holding)
                    <option value="{{ $holding->id }}" @selected((int) old('holding_id', $managedUser->holding_id) === (int) $holding->id)>
                        {{ $holding->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>Regional Holding</span>
            <select name="holding_city_position_id">
                <option value="">Global / tidak dibatasi region</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}" @selected((int) old('holding_city_position_id', $managedUser->holding_city_position_id) === (int) $region->id)>
                        {{ $region->name }} · {{ $region->city?->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>Brand</span>
            <select name="brand_id">
                <option value="">Global / regional holding</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected((int) old('brand_id', $managedUser->brand_id) === (int) $brand->id)>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>City</span>
            <select name="city_id">
                <option value="">Tidak dibatasi kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected((int) old('city_id', $managedUser->city_id) === (int) $city->id)>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="erp-panel-grid">
        <label class="erp-field">
            <span>Branch</span>
            <select name="branch_id">
                <option value="">Tidak dibatasi branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((int) old('branch_id', $managedUser->branch_id) === (int) $branch->id)>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="erp-field">
            <span>Warehouse</span>
            <select name="warehouse_id">
                <option value="">Tidak dibatasi warehouse</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected((int) old('warehouse_id', $managedUser->warehouse_id) === (int) $warehouse->id)>
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </label>
    </section>

    <div class="erp-inline-actions">
        <button type="submit" class="erp-button">Simpan user</button>
        <a class="erp-link" href="{{ route('it.users.index') }}">Kembali</a>
    </div>
</form>
