@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
    <header>
        <p class="erp-kicker">Enterprise RBAC</p>
        <h1>Role & Permission Management</h1>
        <p>Kelola role operasional dan permission matrix untuk seluruh modul ERP.</p>
    </header>

    @can('create', \Modules\Audit\Models\Role::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('it.roles.create') }}">Tambah role</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Scope</th>
                    <th>System</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ $role->name }}</strong><br>
                            <span style="color: var(--erp-muted);">{{ $role->code }}</span>
                        </td>
                        <td><span class="erp-chip">{{ $role->scope_level }}</span></td>
                        <td>{{ $role->is_system ? 'yes' : 'no' }}</td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td><a class="erp-link" href="{{ route('it.roles.show', $role) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada role.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
