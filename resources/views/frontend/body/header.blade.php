
@php
    $setting = App\Models\SiteSetting::current();
@endphp

<header class="top-header top-header-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-2 pr-0">
            </div>

            <div class="col-lg-9 col-md-10">
                <div class="header-right">
                    <ul>
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

  @auth

  <li>
    <i class='bx bx-user'></i>
    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}">My Account</a>
</li>

<li>
    <i class='bx bx-log-out'></i>
    <a href="{{ Auth::user()->role === 'admin' ? route('admin.logout') : route('user.logout') }}">Logout</a>
</li>

  @else

  <li>
    <i class='bx bx-log-in'></i>
    <a href="{{ route('login') }}">Login</a>
</li>

<li>
    <i class='bx bx-user-plus'></i>
    <a href="{{ route('register') }}">Register</a>
</li>

  @endauth


                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
