@extends('frontend.main_master')
@section('title', 'Contact')

@php
    $site = App\Models\SiteSetting::current();
    $phoneHref = $site->phone ? preg_replace('/[^0-9+]/', '', $site->phone) : null;
    $waDigits = preg_replace('/\D+/', '', (string) $site->phone);
    if (str_starts_with($waDigits, '0')) {
        $waDigits = '263' . substr($waDigits, 1);
    }
    $whatsapp = $waDigits !== ''
        ? 'https://wa.me/' . $waDigits . '?text=' . rawurlencode('Hello, I would like to ask about a stay at ' . $site->name . '.')
        : null;
    $mapQuery = $site->address ?: 'Harare, Zimbabwe';
    $directions = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapQuery);
@endphp

@section('main')
<section class="hms-contact-hero">
    <div class="hms-contact-hero__media" style="background-image:url('{{ $site->heroImageUrl() }}')"></div>
    <div class="hms-contact-hero__veil"></div>
    <div class="container hms-contact-hero__copy">
        <p class="hms-kicker">{{ $site->home('contact_kicker') }}</p>
        <h1>{{ $site->home('contact_title') }}</h1>
        <p>{{ $site->home('contact_text') }}</p>
    </div>
</section>

<section class="hms-contact">
    <div class="container">
        <div class="hms-contact__grid">
            <aside class="hms-reach" aria-label="Ways to reach the hotel">
                @if ($phoneHref)
                    <a href="tel:{{ $phoneHref }}">
                        <i class="bx bx-phone-call" aria-hidden="true"></i>
                        <span>
                            <small>Call the desk</small>
                            <strong>{{ $site->phone }}</strong>
                        </span>
                    </a>
                @endif
                @if ($whatsapp)
                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener">
                        <i class="bx bxl-whatsapp" aria-hidden="true"></i>
                        <span>
                            <small>WhatsApp</small>
                            <strong>Message the desk</strong>
                        </span>
                    </a>
                @endif
                @if ($site->email)
                    <a href="mailto:{{ $site->email }}">
                        <i class="bx bx-envelope" aria-hidden="true"></i>
                        <span>
                            <small>Email</small>
                            <strong>{{ $site->email }}</strong>
                        </span>
                    </a>
                @endif
                @if ($site->address)
                    <a href="{{ $directions }}" target="_blank" rel="noopener">
                        <i class="bx bx-map-pin" aria-hidden="true"></i>
                        <span>
                            <small>Find us</small>
                            <strong>{{ $site->address }}</strong>
                        </span>
                    </a>
                @endif
                <div class="hms-reach__quiet">
                    <i class="bx bx-time-five" aria-hidden="true"></i>
                    <span>
                        <small>Hours</small>
                        <strong>{{ $site->home('contact_hours') }}</strong>
                    </span>
                </div>
            </aside>

            <form class="hms-contact__form" method="POST" action="{{ route('store.contact') }}">
                @csrf
                <h2>Send a note</h2>
                <p class="hms-lead">Tell us what you need. We reply by email, usually the same day.</p>

                @if (session('alert-type') === 'success')
                    <div class="hms-contact__thanks" role="status">
                        <strong>Message received.</strong>
                        <p>{{ session('message') }}</p>
                    </div>
                @endif

                <div class="hms-contact__row">
                    <div class="hms-field">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" autocomplete="name" required>
                        @error('name')<p class="hms-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="hms-field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" autocomplete="email" required>
                        @error('email')<p class="hms-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="hms-field">
                    <label for="phone">Phone <span>optional</span></label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" class="form-control" autocomplete="tel">
                    @error('phone')<p class="hms-field-error">{{ $message }}</p>@enderror
                </div>

                <fieldset class="hms-field">
                    <legend>What is this about?</legend>
                    <div class="hms-reasons">
                        @foreach ($reasons as $value => $label)
                            <label class="hms-reason">
                                <input type="radio" name="reason" value="{{ $value }}" @checked(old('reason') === $value) required>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('reason')<p class="hms-field-error">{{ $message }}</p>@enderror
                </fieldset>

                <div class="hms-field">
                    <label for="subject">Subject</label>
                    <input id="subject" type="text" name="subject" value="{{ old('subject') }}" class="form-control" maxlength="120" required>
                    @error('subject')<p class="hms-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="hms-field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" rows="6" maxlength="3000" required>{{ old('message') }}</textarea>
                    @error('message')<p class="hms-field-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="hms-contact__submit">Send message</button>
                <p class="hms-contact__fine">For a room tonight, call the desk. This note stays with the hotel.</p>
            </form>
        </div>
    </div>
</section>

<section class="hms-visit">
    <div class="container">
        <div class="hms-visit__frame">
            <iframe
                title="Map of {{ $mapQuery }}"
                src="https://maps.google.com/maps?q={{ rawurlencode($mapQuery) }}&z=16&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            <div class="hms-visit__photo" style="background-image:url('{{ asset('frontend/assets/img/hotel/lobby.jpg') }}')">
                <div class="hms-visit__caption">
                    <small>The hotel</small>
                    <strong>{{ $site->address ?: $site->name }}</strong>
                    <a href="{{ $directions }}" target="_blank" rel="noopener">Get directions</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
