@extends('frontend.main_master')
@section('title', 'About Us')

@php
    $site = App\Models\SiteSetting::current();
    $team = App\Models\Team::latest()->limit(4)->get();
    $roomCount = App\Models\RoomNumber::where('status', 'Active')->count();
    $guestCount = App\Models\Booking::distinct('email')->count('email');
@endphp

@section('main')
@include('frontend.body.page_head', ['kicker' => $site->home('about_kicker'), 'title' => 'About '.$site->name])

<section class="hms-band hms-band--paper">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hms-section-head hms-section-head--left">
                    <h2>{{ $site->aboutTitle() }}</h2>
                </div>
                <p class="hms-lead">{{ $site->aboutText() }}</p>
                <ul class="hms-checklist mt-4">
                    @foreach (['about_point_1', 'about_point_2', 'about_point_3'] as $point)
                        @if ($site->home($point))
                            <li><i class='bx bx-check-circle'></i> {{ $site->home($point) }}</li>
                        @endif
                    @endforeach
                </ul>
                <a href="{{ route('froom.all') }}" class="hms-text-link mt-4">View rooms</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-4">
                    <div class="col-6">
                        <div class="hms-stat">
                            <i class='bx bx-bed'></i>
                            <strong>{{ $roomCount ?: '—' }}</strong>
                            <span>Rooms</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hms-stat">
                            <i class='bx bx-user'></i>
                            <strong>{{ $guestCount ?: '—' }}</strong>
                            <span>Guests booked</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hms-stat">
                            <i class='bx bx-time-five'></i>
                            <strong>{{ $site->home('contact_hours') ?: '24/7' }}</strong>
                            <span>Front desk</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hms-stat">
                            <i class='bx bx-shield-quarter'></i>
                            <strong>{{ $site->default_currency ?: 'USD' }}</strong>
                            <span>Room rates</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hms-band hms-band--white">
    <div class="container">
        <div class="hms-section-head">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('about_why_kicker') }}</p>
            <h2>{{ $site->home('about_why_title') }}</h2>
        </div>
        <div class="row g-4">
            @foreach ([1 => 'bx-home-alt', 2 => 'bx-wallet', 3 => 'bx-user-voice'] as $n => $icon)
                <div class="col-lg-4 col-md-6">
                    <div class="hms-feature">
                        <i class='bx {{ $icon }}'></i>
                        <h3>{{ $site->home('about_feature_'.$n.'_title') }}</h3>
                        <p>{{ $site->home('about_feature_'.$n.'_text') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if ($team->isNotEmpty())
    @include('frontend.home.team')
@endif

<section class="hms-band hms-invite">
    <div class="container">
        <div class="hms-invite__copy">
            <h2>{{ $site->home('about_cta_title') }}</h2>
            <p>{{ $site->home('about_cta_text') }}</p>
            <a href="{{ route('froom.all') }}" class="hms-invite__btn">Check availability</a>
        </div>
    </div>
</section>
@endsection
