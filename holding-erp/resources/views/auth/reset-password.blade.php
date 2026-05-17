@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <div class="erp-form-card">
        <p class="erp-kicker">Account recovery</p>
        <h2>Buat password baru</h2>

        <form method="POST" action="{{ route('password.update') }}" class="erp-form-stack">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label class="erp-field">
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email', $email) }}" required>
                @error('email')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label class="erp-field">
                <span>Password baru</span>
                <input type="password" name="password" required>
                @error('password')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label class="erp-field">
                <span>Konfirmasi password</span>
                <input type="password" name="password_confirmation" required>
            </label>

            <button class="erp-button" type="submit">Simpan password</button>
        </form>
    </div>
@endsection
