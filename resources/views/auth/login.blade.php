@extends('auth.layout')
@section('title', 'Sign in')
@section('content')
    <h1>Welcome back</h1>
    <p class="lead">Sign in to manage your stays.</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="field">
            <label for="login">Email, username or phone</label>
            <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus autocomplete="username" @error('login') aria-invalid="true" @enderror>
            @error('login')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="password">Password</label>
            <div class="pw">
                <input type="password" name="password" id="password" required autocomplete="current-password" @error('password') aria-invalid="true" @enderror>
                <button type="button" aria-pressed="false">Show</button>
            </div>
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="row-split">
            <label class="check"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Keep me signed in</label>
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>
        <button type="submit" class="btn">Sign in</button>
    </form>
    <p class="alt">New here? <a href="{{ route('register') }}">Create an account</a></p>
@endsection
