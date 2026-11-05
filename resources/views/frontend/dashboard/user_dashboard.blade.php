@extends('frontend.main_master')
@section('title', 'My Account')
@section('main')

@include('frontend.body.page_head', ['title' => 'Welcome back, '.\Illuminate\Support\Str::before(Auth::user()->name, ' '), 'class' => 'hms-page-head--booking'])

<div class="pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                @include('frontend.dashboard.user_menu')
            </div>

            <div class="col-lg-9">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="hms-kpi"><i class="bx bx-calendar"></i><div><strong>{{ $totalBookings }}</strong><span>Total bookings</span></div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="hms-kpi"><i class="bx bx-time-five"></i><div><strong>{{ $pendingBookings }}</strong><span>Pending</span></div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="hms-kpi"><i class="bx bx-check-circle"></i><div><strong>{{ $completedBookings }}</strong><span>Confirmed</span></div></div>
                    </div>
                </div>

                @if ($nextStay)
                    <div class="hms-card">
                        <h3>Your next stay</h3>
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <h4 class="mb-1">{{ $nextStay->room->type->name ?? 'Room' }}</h4>
                                <p class="text-muted mb-2">Booking #{{ $nextStay->code }} · {{ $nextStay->number_of_rooms }} room(s) · {{ $nextStay->persion }} guest(s)</p>
                                @include('frontend.dashboard._booking_badges', ['item' => $nextStay])
                            </div>
                            <div class="col-md-5">
                                <div class="hms-dates m-0">
                                    <div><small>Check-in</small><strong>{{ \Carbon\Carbon::parse($nextStay->check_in)->format('D, d M') }}</strong></div>
                                    <div><small>Check-out</small><strong>{{ \Carbon\Carbon::parse($nextStay->check_out)->format('D, d M') }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="hms-card">
                    <h3 class="d-flex justify-content-between align-items-center">
                        Recent bookings
                        <a href="{{ route('user.booking') }}" class="fs-6">View all</a>
                    </h3>
                    @include('frontend.dashboard._booking_table', ['bookings' => $recentBookings])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
