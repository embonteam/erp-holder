@extends('layouts.app')

@section('title', 'Audit Log Detail')

@section('content')
    <header>
        <p class="erp-kicker">Audit Detail</p>
        <h1>{{ $activityLog->event }}</h1>
        <p>{{ $activityLog->created_at?->format('d M Y H:i:s') }} · {{ $activityLog->user?->name ?? 'System' }}</p>
    </header>

    <section class="erp-panel-grid" style="margin-top: 2rem;">
        <article class="erp-card">
            <p class="erp-kicker">Subject</p>
            <strong>{{ $activityLog->subjectLabel() }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">IP Address</p>
            <strong>{{ $activityLog->ip_address ?: '-' }}</strong>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">User Agent</p>
            <strong>{{ str($activityLog->user_agent ?: '-')->limit(60) }}</strong>
        </article>
    </section>

    <section class="erp-panel-grid" style="margin-top: 1rem;">
        <article class="erp-card">
            <p class="erp-kicker">Old Values</p>
            <pre class="erp-json-block">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">New Values</p>
            <pre class="erp-json-block">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
        </article>
        <article class="erp-card">
            <p class="erp-kicker">Metadata</p>
            <pre class="erp-json-block">{{ json_encode($activityLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
        </article>
    </section>

    <p style="margin-top: 1.5rem;">
        <a class="erp-link" href="{{ route('audit.activity-logs.index') }}">Kembali ke audit logs</a>
    </p>
@endsection
