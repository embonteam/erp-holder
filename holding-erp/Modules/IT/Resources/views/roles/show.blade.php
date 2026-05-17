@extends('layouts.app')

@section('title', 'Role '.$role->code)

@section('content')
    <header>
        <p class="erp-kicker">Role Detail</p>
        <h1>{{ $role->name }}</h1>
        <p>{{ $role->code }} · <span class="erp-chip">{{ $role->scope_level }}</span> · {{ $role->is_system ? 'system role' : 'custom role' }}</p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Permissions</p>
            <strong>{{ $role->permissions->count() }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Users</p>
            <strong>{{ $role->users_count }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Locked</p>
            <strong>{{ $role->code === 'owner' ? 'yes' : 'no' }}</strong>
        </article>
    </section>

    <div class="erp-inline-actions" style="margin-top: 1.5rem;">
        @can('update', $role)
            <a class="erp-button erp-button-link" href="{{ route('it.roles.edit', $role) }}">Edit role & permissions</a>
        @else
            <span class="erp-chip">Role ini dikunci untuk menjaga akses owner.</span>
        @endcan

        <a class="erp-link" href="{{ route('it.roles.index') }}">Kembali ke daftar</a>
    </div>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Permission</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($role->permissions as $permission)
                    <tr>
                        <td>{{ $permission->module }}</td>
                        <td>
                            <strong>{{ $permission->code }}</strong><br>
                            <span style="color: var(--erp-muted);">{{ $permission->name }}</span>
                        </td>
                        <td>{{ $permission->action }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Role ini belum memiliki permission.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
