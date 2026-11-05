@extends('frontend.main_master')
@php($site = App\Models\SiteSetting::current())
@section('main')

<section class="hms-hero">
    <div class="hms-hero__media" style="background-image:url('{{ $site->heroImageUrl() }}')"></div>
    <div class="hms-hero__veil"></div>
    <div class="container hms-hero__copy">
        <p class="hms-kicker">{{ $site->home('hero_kicker') ?: $site->name }}</p>
        <h1>{{ $site->home('hero_title') }}</h1>
        <p>{{ $site->home('hero_text') }}</p>
        @if ($site->address || $site->phone)
            <ul class="hms-hero__facts">
                @if ($site->address)
                    <li><i class="bx bx-map-pin"></i><a href="{{ route('contact.us') }}">{{ $site->address }}</a></li>
                @endif
                @if ($site->phone)
                    <li><i class="bx bx-phone-call"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', $site->phone) }}">{{ $site->phone }}</a></li>
                @endif
            </ul>
        @endif
    </div>
    <div class="container">
        <form class="hms-book" method="get" action="{{ route('booking.search') }}">
            <div class="hms-book__field">
                <label for="check_in">Check in</label>
                <input id="check_in" autocomplete="off" type="text" required name="check_in" class="dt_picker" placeholder="Select a date">
            </div>
            <div class="hms-book__field">
                <label for="check_out">Check out</label>
                <input id="check_out" autocomplete="off" type="text" required name="check_out" class="dt_picker" placeholder="Select a date">
            </div>
            <div class="hms-book__field">
                <label for="persion">Guests</label>
                <select id="persion" name="persion">
                    <option value="1">1 guest</option>
                    <option value="2" selected>2 guests</option>
                    <option value="3">3 guests</option>
                    <option value="4">4 guests</option>
                </select>
            </div>
            <button type="submit" class="hms-book__go">Check availability</button>
            @if ($site->home('booking_note'))
                <p class="hms-book__note">{{ $site->home('booking_note') }}</p>
            @endif
        </form>
    </div>
</section>

@include('frontend.home.room_area')

@php($moments = App\Models\Gallery::where('show_on_home', true)->orderBy('id')->limit(3)->get())
@if ($moments->isNotEmpty())
<section class="hms-band hms-band--white hms-moments">
    <div class="container">
        <div class="hms-section-head hms-section-head--left">
            <p class="hms-kicker hms-kicker--dark">{{ $site->home('moments_kicker') }}</p>
            <h2>{{ $site->home('moments_title') }}</h2>
        </div>
        <div class="hms-moments__grid">
            @foreach ($moments as $moment)
                <figure>
                    <img src="{{ asset($moment->photo_name) }}" alt="{{ $moment->caption ?: $site->name }}">
                    @if ($moment->caption)
                        <figcaption>{{ $moment->caption }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

@include('frontend.home.room_area_two')
@include('frontend.home.services')
@include('frontend.home.team')
@include('frontend.home.testimonials')
@include('frontend.home.faq')
@include('frontend.home.blog')

@endsection
