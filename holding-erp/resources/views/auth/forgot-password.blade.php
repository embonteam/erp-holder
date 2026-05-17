@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <div class="erp-form-card">
        <p class="erp-kicker">Account recovery</p>
        <h2>Reset password</h2>

        @if (session('status'))
            <p class="erp-alert erp-alert-success">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="erp-form-stack">
            @csrf

            <label class="erp-field">
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <button class="erp-button" type="submit">Kirim tautan reset</button>
        </form>

        <a class="erp-link" href="{{ route('login') }}">Kembali ke login</a>
    </div>
@endsection
