@extends('auth.layout')
@section('title', 'Choose a new password')
@section('side_title', 'A fresh start.')
@section('side_text', 'Pick a password you have not used elsewhere.')
@section('content')
    <h1>Choose a new password</h1>
    <p class="lead">Then you can sign in as usual.</p>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="field">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required autocomplete="username" @error('email') aria-invalid="true" @enderror>
            @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="password">New password</label>
            <div class="pw">
                <input type="password" name="password" id="password" required autofocus autocomplete="new-password" @error('password') aria-invalid="true" @enderror>
                <button type="button" aria-pressed="false">Show</button>
            </div>
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')<div class="err">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn">Save new password</button>
    </form>
@endsection
