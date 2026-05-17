@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
    <header>
        <p class="erp-kicker">Enterprise RBAC</p>
        <h1>Tambah Role</h1>
        <p>Buat role baru dan pilih permission yang sesuai.</p>
    </header>

    @include('it::roles._form', [
        'action' => route('it.roles.store'),
        'method' => 'POST',
    ])
@endsection
