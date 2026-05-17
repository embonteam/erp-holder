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

@php
    $selectedPermissions = collect(old('permissions', $role->exists ? $role->permissions->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
@endphp

<form method="POST" action="{{ $action }}" class="erp-form-card" style="margin-top: 2rem;">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <section class="erp-panel-grid">
        <label class="erp-field">
            <span>Kode role</span>
            <input type="text" name="code" value="{{ old('code', $role->code) }}" @readonly($role->exists && $role->is_system) required>
        </label>

        <label class="erp-field">
            <span>Nama role</span>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" required>
        </label>

        <label class="erp-field">
            <span>Scope level</span>
            <select name="scope_level" required>
                @foreach ($scopeLevels as $scopeLevel)
                    <option value="{{ $scopeLevel }}" @selected(old('scope_level', $role->scope_level) === $scopeLevel)>{{ $scopeLevel }}</option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="erp-card">
        <p class="erp-kicker">Permission Matrix</p>
        <p style="margin-top: 0.25rem; color: var(--erp-muted);">Centang permission yang diberikan untuk role ini.</p>

        <div class="erp-permission-grid">
            @foreach ($permissionGroups as $module => $permissions)
                <article class="erp-permission-group">
                    <h3>{{ str($module)->headline() }}</h3>
                    @foreach ($permissions as $permission)
                        <label class="erp-checkbox erp-permission-item">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                @checked(in_array((int) $permission->id, $selectedPermissions, true))
                            >
                            <span>
                                <strong>{{ $permission->code }}</strong><br>
                                <small>{{ $permission->name }}</small>
                            </span>
                        </label>
                    @endforeach
                </article>
            @endforeach
        </div>
    </section>

    <div class="erp-inline-actions">
        <button type="submit" class="erp-button">Simpan role</button>
        <a class="erp-link" href="{{ route('it.roles.index') }}">Kembali</a>
    </div>
</form>
