@extends('auth.layout')
@section('title', 'Reset password')
@section('side_title', 'Locked out? It happens.')
@section('side_text', 'We will email you a link to choose a new password.')
@section('content')
    <h1>Reset your password</h1>
    <p class="lead">Enter the email on your account and we will send you a reset link.</p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" @error('email') aria-invalid="true" @enderror>
            @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn">Send reset link</button>
    </form>
    <p class="alt">Remembered it? <a href="{{ route('login') }}">Back to sign in</a></p>
@endsection
