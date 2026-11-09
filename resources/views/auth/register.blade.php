@extends('auth.layout')
@section('title', 'Create account')
@section('side_title', 'Book once, return easily.')
@section('side_text', 'An account keeps your reservations, invoices and details together, so the next stay takes a minute.')
@section('content')
    <h1>Create your account</h1>
    <p class="lead">Takes under a minute. You can book straight after.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div class="field">
            <label for="name">Full name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name" @error('name') aria-invalid="true" @enderror>
            @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" @error('email') aria-invalid="true" @enderror>
            @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="password">Password</label>
            <div class="pw">
                <input type="password" name="password" id="password" required autocomplete="new-password" @error('password') aria-invalid="true" @enderror>
                <button type="button" aria-pressed="false">Show</button>
            </div>
            <div class="hint">Use at least 8 characters.</div>
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn">Create account</button>
    </form>
    <p class="alt">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
@endsection
