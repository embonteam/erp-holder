@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="erp-form-card">
        <p class="erp-kicker">Secure access</p>
        <h2>Masuk ke Holding ERP</h2>

        @if (session('status'))
            <p class="erp-alert erp-alert-success">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}" class="erp-form-stack">
            @csrf

            <label class="erp-field">
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label class="erp-field">
                <span>Password</span>
                <input type="password" name="password" required>
                @error('password')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label class="erp-checkbox">
                <input type="checkbox" name="remember" value="1">
                <span>Ingat perangkat ini</span>
            </label>

            <button class="erp-button" type="submit">Masuk</button>
        </form>

        <a class="erp-link" href="{{ route('password.request') }}">Lupa password?</a>
    </div>
@endsection
