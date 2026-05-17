@extends('layouts.app')

@section('title', 'Approval Inbox')

@section('content')
    <header>
        <p class="erp-kicker">Enterprise Work Queue</p>
        <h1>Approval Inbox</h1>
        <p>Semua pekerjaan yang membutuhkan keputusan muncul di sini, sesuai role dan scope user.</p>
    </header>

    <section class="erp-table-card" style="margin-top: 2rem;">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Referensi</th>
                    <th>Keterangan</th>
                    <th>Masuk</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($approvalItems as $item)
                    <tr>
                        <td><span class="erp-chip">{{ str($item['type'])->headline() }}</span></td>
                        <td>
                            <strong>{{ $item['reference'] }}</strong><br>
                            <span style="color: var(--erp-muted);">{{ $item['title'] }}</span>
                        </td>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['created_at']?->diffForHumans() }}</td>
                        <td><a class="erp-link" href="{{ $item['route'] }}">Review</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada approval yang menunggu keputusan. Tenang dulu, sistem sedang ramah. ??</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
