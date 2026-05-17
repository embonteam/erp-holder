<section class="erp-table-card" style="margin-top: 2rem;">
    <div style="padding: 1rem 1rem 0;">
        <p class="erp-kicker">Activity Timeline</p>
        <h2 style="margin: 0.2rem 0 1rem; font-size: 1.2rem;">Jejak aktivitas terakhir</h2>
    </div>

    <table class="erp-table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Event</th>
                <th>User</th>
                <th>Perubahan</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activityLogs as $activityLog)
                <tr>
                    <td>{{ $activityLog->created_at?->format('d M Y H:i') }}</td>
                    <td><span class="erp-chip">{{ $activityLog->event }}</span></td>
                    <td>{{ $activityLog->user?->name ?? 'System' }}</td>
                    <td>
                        @if ($activityLog->new_values)
                            {{ collect($activityLog->new_values)->keys()->take(4)->implode(', ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if (Route::has('audit.activity-logs.show') && auth()->user()?->hasPermission('audit.log.view'))
                            <a class="erp-link" href="{{ route('audit.activity-logs.show', $activityLog) }}">Audit detail</a>
                        @else
                            <span style="color: var(--erp-muted);">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada aktivitas tercatat untuk data ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
