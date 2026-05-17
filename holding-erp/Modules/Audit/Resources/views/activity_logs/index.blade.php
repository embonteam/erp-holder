@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
    <header>
        <p class="erp-kicker">Audit & Compliance</p>
        <h1>Activity Logs</h1>
        <p>Jejak aktivitas kritis: auth, approval, stock movement, supplier changes, permission changes, dan event operasional lain.</p>
    </header>

    <form method="GET" action="{{ route('audit.activity-logs.index') }}" class="erp-form-card erp-filter-card" style="margin-top: 2rem;">
        <label class="erp-field">
            <span>Event</span>
            <input type="text" name="event" value="{{ $filters['event'] ?? '' }}" list="audit-events" placeholder="Contoh: purchasing.purchase.approved">
            <datalist id="audit-events">
                @foreach ($events as $event)
                    <option value="{{ $event }}"></option>
                @endforeach
            </datalist>
        </label>

        <label class="erp-field">
            <span>Subject</span>
            <input type="text" name="subject" value="{{ $filters['subject'] ?? '' }}" placeholder="Class name atau ID">
        </label>

        <div class="erp-inline-actions">
            <button type="submit" class="erp-button">Filter</button>
            <a class="erp-link" href="{{ route('audit.activity-logs.index') }}">Reset</a>
        </div>
    </form>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Event</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Context</th>
                    <th>IP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
                        <td><span class="erp-chip">{{ $log->event }}</span></td>
                        <td>{{ $log->user?->name ?? 'System' }}</td>
                        <td>{{ $log->subjectLabel() }}</td>
                        <td>
                            @if ($log->metadata)
                                {{ collect($log->metadata)->map(fn ($value, $key) => $key.': '.$value)->take(3)->implode(' · ') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $log->ip_address ?: '-' }}</td>
                        <td><a class="erp-link" href="{{ route('audit.activity-logs.show', $log) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada activity log.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div style="margin-top: 1rem;">
        {{ $logs->links() }}
    </div>
@endsection
