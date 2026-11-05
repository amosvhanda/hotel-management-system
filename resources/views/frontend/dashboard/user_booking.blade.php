@extends('frontend.main_master')
@section('title', 'My Bookings')
@section('main')

<div class="inner-banner inner-bg6">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li><a href="{{ route('dashboard') }}">My Account</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>My Bookings</li>
            </ul>
            <h3>My Bookings</h3>
        </div>
    </div>
</div>

<div class="pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                @include('frontend.dashboard.user_menu')
            </div>
            <div class="col-lg-9">
                <div class="hms-card">
                    <h3>All bookings</h3>
                    @include('frontend.dashboard._booking_table', ['bookings' => $allData])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
