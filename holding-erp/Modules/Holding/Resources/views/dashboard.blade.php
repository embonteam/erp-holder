@extends('layouts.app')

@section('title', 'Holding Dashboard')
@section('theme', 'theme-holding')

@section('content')
    <header>
        <p class="erp-kicker">Holding overview</p>
        <h1>Holding Dashboard</h1>
        <p>Enterprise shell is ready for revenue, approvals, and live operations.</p>
    </header>

    <div
        id="holding-dashboard-widgets"
        style="margin-top: 2rem;"
        data-revenue-today="Rp0"
        data-pending-approvals="{{ $pendingApprovals }}"
        data-critical-stock="{{ $criticalStock }}"
        data-active-brands="{{ $activeBrands }}"
        data-unread-notifications="{{ $unreadNotifications }}"
    ></div>
@endsection
