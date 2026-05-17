@extends('layouts.app')

@section('title', 'User '.$managedUser->email)

@section('content')
    <header>
        <p class="erp-kicker">User Detail</p>
        <h1>{{ $managedUser->name }}</h1>
        <p>{{ $managedUser->email }} · <span class="erp-chip">{{ $managedUser->is_active ? 'active' : 'inactive' }}</span></p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Role</p>
            <strong>{{ $managedUser->role?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Holding</p>
            <strong>{{ $managedUser->holding?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Regional</p>
            <strong>{{ $managedUser->holdingCityPosition?->name ?? '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Last Login</p>
            <strong>{{ $managedUser->last_login_at?->diffForHumans() ?? '-' }}</strong>
        </article>
    </section>

    <section class="erp-panel-grid" style="margin-top: 1rem;">
        <article class="erp-card">
            <p class="erp-kicker">Brand</p>
            <strong>{{ $managedUser->brand?->name ?? 'Global / regional' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">City</p>
            <strong>{{ $managedUser->city?->name ?? 'Tidak dibatasi' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Branch</p>
            <strong>{{ $managedUser->branch?->name ?? 'Tidak dibatasi' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Warehouse</p>
            <strong>{{ $managedUser->warehouse?->name ?? 'Tidak dibatasi' }}</strong>
        </article>
    </section>

    <div class="erp-inline-actions" style="margin-top: 1.5rem;">
        @can('update', $managedUser)
            <a class="erp-button erp-button-link" href="{{ route('it.users.edit', $managedUser) }}">Edit user</a>
        @endcan

        @if ($managedUser->is_active)
            @can('deactivate', $managedUser)
                <form method="POST" action="{{ route('it.users.deactivate', $managedUser) }}">
                    @csrf
                    <button type="submit" class="erp-button" style="background: var(--erp-danger);">Nonaktifkan</button>
                </form>
            @endcan
        @else
            @can('reactivate', $managedUser)
                <form method="POST" action="{{ route('it.users.reactivate', $managedUser) }}">
                    @csrf
                    <button type="submit" class="erp-button">Aktifkan kembali</button>
                </form>
            @endcan
        @endif

        <a class="erp-link" href="{{ route('it.users.index') }}">Kembali ke daftar</a>
    </div>

    @include('shared.activity-timeline', ['activityLogs' => $activityLogs])
@endsection
