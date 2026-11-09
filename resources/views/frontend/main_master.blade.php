<!doctype html>
@php($site = App\Models\SiteSetting::current())
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Required Meta Tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="author" content="Amos Vhanda">
        <!-- jQuery first, so inline scripts inside pages can use it -->
        <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
        <meta name="description" content="{{ Str::limit(strip_tags(trim(View::yieldContent('meta_description')) ?: ($site->meta_description ?: ($site->tagline ?: 'Book your stay at ' . $site->name))), 300) }}">
        @include('partials.seo')
        @if ($site->meta_keywords)<meta name="keywords" content="{{ $site->meta_keywords }}">@endif

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
        <!-- Animate Min CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}">
        <!-- Flaticon CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/flaticon.css') }}">
        <!-- Boxicons CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/boxicons.min.css') }}">
        <!-- Magnific Popup CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/magnific-popup.css') }}">
        <!-- Owl Carousel Min CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.theme.default.min.css') }}">
        <!-- Nice Select Min CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/nice-select.min.css') }}">
        <!-- Meanmenu CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/meanmenu.css') }}">
        <!-- Jquery Ui CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery-ui.css') }}">
        <!-- Style CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
        <!-- Responsive CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
        <!-- Theme Dark CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/theme-dark.css') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT@0,9..144,300;0,9..144,400;0,9..144,500;1,9..144,300&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('hms-tokens.css') }}?v=2">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/hms.css') }}?v=10">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/hms-premium.css') }}?v=13">
        @include('partials.theme-vars')
        <script>
            (function () {
                var saved = null;
                try { saved = localStorage.getItem('theme'); } catch (e) {}
                var dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = saved === 'theme-dark' || saved === 'theme-light'
                    ? saved
                    : (dark ? 'theme-dark' : 'theme-light');
                document.documentElement.className = theme;
            })();
        </script>

        <!-- Favicon -->
        @include('partials.favicons', ['iconSite' => $site])

        	<!-- toastr CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/toastr.css') }}" >
    <!-- toastr CSS -->

        <title>@hasSection('title')@yield('title') | @endif{{ $site->meta_title ?: $site->name }}</title>
        @if ($site->custom_css)<style>{!! $site->custom_css !!}</style>@endif
    </head>
    <body>

        <!-- PreLoader Start -->
        <div class="preloader">
            <div class="d-table">
                <div class="d-table-cell">
                    <div class="sk-cube-area">
                        <div class="sk-cube1 sk-cube"></div>
                        <div class="sk-cube2 sk-cube"></div>
                        <div class="sk-cube4 sk-cube"></div>
                        <div class="sk-cube3 sk-cube"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- PreLoader End -->

        <!-- Top Header Start -->
        @include('frontend.body.header')
        <!-- Top Header End -->

        <!-- Start Navbar Area -->
        @include('frontend.body.navbar')
        <!-- End Navbar Area -->

        @yield('main')

        <!-- Footer Area -->
        @include('frontend.body.footer')
        <!-- Footer Area End -->


        <!-- Jquery Min JS -->
        <!-- Bootstrap Bundle Min JS -->
        <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Magnific Popup Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.magnific-popup.min.js') }}"></script>
        <!-- Owl Carousel Min JS -->
        <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
        <!-- Nice Select Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.nice-select.min.js') }}"></script>
        <!-- Wow Min JS -->
        <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
        <!-- Jquery Ui JS -->
        <script src="{{ asset('frontend/assets/js/jquery-ui.js') }}"></script>
        <!-- Meanmenu JS -->
        <script src="{{ asset('frontend/assets/js/meanmenu.js') }}"></script>
        <!-- Ajaxchimp Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.ajaxchimp.min.js') }}"></script>
        <!-- Form Validator Min JS -->
        <script src="{{ asset('frontend/assets/js/form-validator.min.js') }}"></script>
        <!-- Contact Form JS -->
        <script src="{{ asset('frontend/assets/js/contact-form-script.js') }}"></script>
        <!-- Custom JS -->
        <script src="{{ asset('frontend/assets/js/custom.js') }}?v=9"></script>
        <script src="{{ asset('frontend/assets/js/hms-premium.js') }}?v=3"></script>

         <script src="{{ asset('frontend/assets/js/toastr.min.js') }}"></script>


<script>
    @if (Session::has('message'))
        (function () {
            var type = @json(Session::get('alert-type', 'info'));
            var msg = @json(Session::get('message'));
            (toastr[type] || toastr.info)(msg);
        })();
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error(@json($error));
        @endforeach
    @endif
</script>
@stack('scripts')


        @if ($site->whatsappDigits())
            <a class="hms-wa-float" href="{{ \App\Support\Phone::whatsappLink($site->whatsappDigits(), 'Hi ' . $site->name . ', I have a question about a stay.') }}"
               target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp"><i class="bx bxl-whatsapp"></i><span>Chat on WhatsApp</span></a>
            <style>
                .hms-wa-float{position:fixed;right:18px;bottom:18px;z-index:999;display:flex;align-items:center;gap:8px;background:#25d366;color:#fff!important;
                    border-radius:999px;padding:10px 16px 10px 12px;font-weight:600;box-shadow:0 6px 18px rgba(0,0,0,.18);text-decoration:none}
                .hms-wa-float i{font-size:26px;line-height:1}
                .hms-wa-float:hover{background:#1ebe5b}
                @media (max-width:575px){.hms-wa-float span{display:none}.hms-wa-float{padding:12px}}
            </style>
        @endif
    </body>
</html>
