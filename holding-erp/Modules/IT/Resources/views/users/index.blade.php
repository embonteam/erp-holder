@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <header>
        <p class="erp-kicker">Legal & IT Access Control</p>
        <h1>User Management</h1>
        <p>Kelola user, role, dan scope akses holding/brand/city/branch/warehouse.</p>
    </header>

    @can('create', \App\Models\User::class)
        <p style="margin-top: 1rem;">
            <a class="erp-button erp-button-link" href="{{ route('it.users.create') }}">Tambah user</a>
        </p>
    @endcan

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Scope</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $managedUser)
                    <tr>
                        <td>
                            <strong>{{ $managedUser->name }}</strong><br>
                            <span style="color: var(--erp-muted);">{{ $managedUser->email }}</span>
                        </td>
                        <td>{{ $managedUser->role?->name ?? '-' }}</td>
                        <td>
                            {{ $managedUser->brand?->code ?? 'Holding' }}
                            @if ($managedUser->city)
                                · {{ $managedUser->city->name }}
                            @endif
                            @if ($managedUser->branch)
                                · {{ $managedUser->branch->name }}
                            @endif
                            @if ($managedUser->warehouse)
                                · {{ $managedUser->warehouse->name }}
                            @endif
                        </td>
                        <td><span class="erp-chip">{{ $managedUser->is_active ? 'active' : 'inactive' }}</span></td>
                        <td>{{ $managedUser->last_login_at?->diffForHumans() ?? '-' }}</td>
                        <td><a class="erp-link" href="{{ route('it.users.show', $managedUser) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div style="margin-top: 1rem;">
        {{ $users->links() }}
    </div>
@endsection
