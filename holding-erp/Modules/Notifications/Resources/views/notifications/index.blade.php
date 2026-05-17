@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <header>
        <p class="erp-kicker">Enterprise Notification Center</p>
        <h1>Notifications</h1>
        <p>Alert operasional, approval, inventory, finance, tax, dan reminder penting akan terkonsolidasi di sini.</p>
    </header>

    <div class="erp-inline-actions" style="margin-top: 1rem; align-items: center;">
        <span class="erp-chip">{{ $unreadCount }} unread</span>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="erp-button">Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Notifikasi</th>
                    <th>Severity</th>
                    <th>Waktu</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr @class(['is-unread' => $notification->read_at === null])>
                        <td>
                            <span class="erp-chip">{{ $notification->read_at ? 'read' : 'unread' }}</span>
                        </td>
                        <td>
                            <strong>{{ $notification->title }}</strong><br>
                            <span style="color: var(--erp-muted);">{{ $notification->message }}</span>
                        </td>
                        <td><span class="erp-chip erp-chip-{{ $notification->severity }}">{{ $notification->severity }}</span></td>
                        <td>{{ $notification->created_at?->diffForHumans() }}</td>
                        <td>
                            @if ($notification->read_at === null)
                                <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="erp-button">Dibaca</button>
                                </form>
                            @else
                                <span style="color: var(--erp-muted);">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada notifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div style="margin-top: 1rem;">
        {{ $notifications->links() }}
    </div>
@endsection
