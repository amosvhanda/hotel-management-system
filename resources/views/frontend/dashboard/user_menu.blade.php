@php
    $me = Auth::user();
    $link = fn ($route) => request()->routeIs($route) ? 'active' : '';
@endphp

<div class="hms-card">
    <div class="hms-account-head">
        <img src="{{ $me->photo ? url('upload/user_images/' . $me->photo) : url('upload/no_image.jpg') }}" alt="{{ $me->name }}">
        <h4>{{ $me->name }}</h4>
        <p>{{ $me->email }}</p>
    </div>

    <ul class="hms-account-nav">
        <li><a href="{{ route('dashboard') }}" class="{{ $link('dashboard') }}"><i class="bx bx-grid-alt"></i> Overview</a></li>
        <li><a href="{{ route('user.booking') }}" class="{{ $link('user.booking') }}"><i class="bx bx-calendar-check"></i> My Bookings</a></li>
        <li><a href="{{ route('user.profile') }}" class="{{ $link('user.profile') }}"><i class="bx bx-user"></i> My Profile</a></li>
        <li><a href="{{ route('user.change.password') }}" class="{{ $link('user.change.password') }}"><i class="bx bx-lock-alt"></i> Change Password</a></li>
        <li><a href="{{ route('user.logout') }}"><i class="bx bx-log-out"></i> Sign Out</a></li>
    </ul>
</div>
