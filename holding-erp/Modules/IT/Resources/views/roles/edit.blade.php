@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <header>
        <p class="erp-kicker">Enterprise RBAC</p>
        <h1>Edit Role</h1>
        <p>{{ $role->name }} · {{ $role->code }}</p>
    </header>

    @include('it::roles._form', [
        'action' => route('it.roles.update', $role),
        'method' => 'PUT',
    ])
@endsection
