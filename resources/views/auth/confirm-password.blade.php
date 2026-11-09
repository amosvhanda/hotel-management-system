@extends('auth.layout')
@section('title', 'Confirm password')
@section('side_title', 'Just making sure it is you.')
@section('side_text', 'This step protects sensitive actions on your account.')
@section('content')
    <h1>Confirm your password</h1>
    <p class="lead">Enter your password to continue.</p>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf
        <div class="field">
            <label for="password">Password</label>
            <div class="pw">
                <input type="password" name="password" id="password" required autofocus autocomplete="current-password" @error('password') aria-invalid="true" @enderror>
                <button type="button" aria-pressed="false">Show</button>
            </div>
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn">Confirm password</button>
    </form>
@endsection
