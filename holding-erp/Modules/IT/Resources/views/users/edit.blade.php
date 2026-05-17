@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <header>
        <p class="erp-kicker">Legal & IT Access Control</p>
        <h1>Edit User</h1>
        <p>{{ $managedUser->name }} · {{ $managedUser->email }}</p>
    </header>

    @include('it::users._form', [
        'action' => route('it.users.update', $managedUser),
        'method' => 'PUT',
    ])
@endsection
