@extends('auth.layout')
@section('title', 'Verify your email')
@section('side_title', 'One quick check.')
@section('side_text', 'Confirming your email keeps your bookings and invoices safe.')
@section('content')
    <h1>Check your inbox</h1>
    <p class="lead">We sent a verification link to your email. Open it to finish setting up your account.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="notice" role="status">A new verification link is on its way.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn">Resend verification email</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:12px">
        @csrf
        <button type="submit" class="btn btn-quiet">Sign out</button>
    </form>
@endsection
