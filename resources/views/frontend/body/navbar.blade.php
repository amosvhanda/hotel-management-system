@php
    $setting = App\Models\SiteSetting::current();
    $navRooms = App\Models\Room::with('type')->where('status', 1)->latest()->get();
    $active = fn (...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
@endphp

<div class="navbar-area">
    <!-- Menu For Mobile Device -->
    <div class="mobile-nav">
        <button type="button" class="hms-theme" aria-pressed="false" aria-label="Switch to dark mode">
            <i class="bx bx-moon" aria-hidden="true"></i>
        </button>
        <a href="{{ route('home') }}" class="logo">
            @if ($setting->logo_url)
                <img src="{{ $setting->logo_url }}" class="logo-one" alt="{{ $setting->name }}">
                <img src="{{ $setting->logo_url }}" class="logo-two" alt="{{ $setting->name }}">
            @else
                <strong class="h4 m-0">{{ $setting->name }}</strong>
            @endif
        </a>
    </div>

    <!-- Menu For Desktop Device -->
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light">
                <a class="navbar-brand" href="{{ route('home') }}">
                    @if ($setting->logo_url)
                        <img src="{{ $setting->logo_url }}" class="logo-one" alt="{{ $setting->name }}">
                        <img src="{{ $setting->logo_url }}" class="logo-two" alt="{{ $setting->name }}">
                    @else
                        <strong class="h4 m-0">{{ $setting->name }}</strong>
                    @endif
                </a>

                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    <ul class="navbar-nav m-auto">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ $active('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link {{ $active('about') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('froom.all') }}" class="nav-link {{ $active('froom.all', 'room.details', 'booking.search', 'search_room_details') }}">
                                Rooms
                                @if ($navRooms->isNotEmpty())<i class='bx bx-chevron-down'></i>@endif
                            </a>
                            @if ($navRooms->isNotEmpty())
                                <ul class="dropdown-menu">
                                    @foreach ($navRooms as $item)
                                        <li class="nav-item">
                                            <a href="{{ route('room.details', $item->id) }}" class="nav-link">
                                                {{ $item->type->name ?? 'Room' }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('home') }}#services" class="nav-link {{ $active('service.details') }}">Services</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('show.gallery') }}" class="nav-link {{ $active('show.gallery') }}">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('blog.list') }}" class="nav-link {{ $active('blog.*') }}">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('contact.us') }}" class="nav-link {{ $active('contact.us') }}">Contact</a>
                        </li>

                        <li class="nav-item-btn">
                            <a href="{{ route('froom.all') }}" class="default-btn btn-bg-one border-radius-5">Book Now</a>
                        </li>
                    </ul>

                    <div class="nav-btn">
                        <button type="button" class="hms-theme" aria-pressed="false" aria-label="Switch to dark mode">
                            <i class="bx bx-moon" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('froom.all') }}" class="default-btn btn-bg-one border-radius-5">Book Now</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
