@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <header>
        <p class="erp-kicker">Legal & IT Access Control</p>
        <h1>Tambah User</h1>
        <p>Buat akun operasional dengan role dan scope data yang jelas.</p>
    </header>

    @include('it::users._form', [
        'action' => route('it.users.store'),
        'method' => 'POST',
        'managedUser' => new \App\Models\User(),
    ])
@endsection
