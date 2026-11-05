@php
    $setting = App\Models\SiteSetting::current();
    $socials = collect([
        'facebook'  => 'bxl-facebook',
        'twitter'   => 'bxl-twitter',
        'instagram' => 'bxl-instagram',
        'linkedin'  => 'bxl-linkedin',
        'youtube'   => 'bxl-youtube',
    ])->filter(fn ($icon, $field) => filled($setting->{$field}));
@endphp

<footer class="footer-area footer-bg">
    <div class="container">
        <div class="footer-top pt-100 pb-70">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a href="{{ route('home') }}">
                                @if ($setting->logo_light_url)
                                    <img src="{{ $setting->logo_light_url }}" alt="{{ $setting->name }}" style="max-height:56px">
                                @else
                                    <h3 class="text-white m-0">{{ $setting->name }}</h3>
                                @endif
                            </a>
                        </div>
                        <p>
                            {{ $setting->site_description ?: ($setting->tagline ?: 'Comfortable rooms, warm hospitality and easy online booking.') }}
                        </p>
                        <ul class="footer-list-contact">
                            @if ($setting->address)
                                <li>
                                    <i class='bx bx-map-pin'></i>
                                    <a href="{{ route('contact.us') }}">{{ $setting->address }}</a>
                                </li>
                            @endif
                            @if ($setting->phone)
                                <li>
                                    <i class='bx bx-phone-call'></i>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->phone) }}">{{ $setting->phone }}</a>
                                </li>
                            @endif
                            @if ($setting->email)
                                <li>
                                    <i class='bx bx-envelope'></i>
                                    <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h3>Explore</h3>
                        <ul class="footer-list">
                            <li><a href="{{ route('about') }}"><i class='bx bx-caret-right'></i> About Us</a></li>
                            <li><a href="{{ route('froom.all') }}"><i class='bx bx-caret-right'></i> Rooms &amp; Rates</a></li>
                            <li><a href="{{ route('home') }}#services"><i class='bx bx-caret-right'></i> Services</a></li>
                            <li><a href="{{ route('show.gallery') }}"><i class='bx bx-caret-right'></i> Gallery</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h3>Guests</h3>
                        <ul class="footer-list">
                            @auth
                                <li><a href="{{ route('user.booking') }}"><i class='bx bx-caret-right'></i> My Bookings</a></li>
                                <li><a href="{{ route('user.profile') }}"><i class='bx bx-caret-right'></i> My Profile</a></li>
                            @else
                                <li><a href="{{ route('login') }}"><i class='bx bx-caret-right'></i> Sign In</a></li>
                                <li><a href="{{ route('register') }}"><i class='bx bx-caret-right'></i> Create Account</a></li>
                            @endauth
                            <li><a href="{{ route('blog.list') }}"><i class='bx bx-caret-right'></i> Blog</a></li>
                            <li><a href="{{ route('contact.us') }}"><i class='bx bx-caret-right'></i> Contact Us</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h3>Plan Your Stay</h3>
                        <p>{{ $setting->home('footer_plan') }}</p>
                        <a href="{{ route('froom.all') }}" class="default-btn btn-bg-one border-radius-5 mt-2">Check Availability</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="copy-right-area">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-8">
                    <div class="copy-right-text text-align1">
                        <p>{{ $setting->copyright_text }} <span class="ms-2" style="opacity:.7">Developed by Amos Vhanda</span></p>
                    </div>
                </div>

                @if ($socials->isNotEmpty())
                    <div class="col-lg-4 col-md-4">
                        <div class="social-icon text-align2">
                            <ul class="social-link">
                                @foreach ($socials as $field => $icon)
                                    <li>
                                        <a href="{{ $setting->{$field} }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($field) }}"><i class='bx {{ $icon }}'></i></a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</footer>
