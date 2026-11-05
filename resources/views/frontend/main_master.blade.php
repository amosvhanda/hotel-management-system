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
        <meta name="description" content="{{ $site->meta_description ?: ($site->tagline ?: 'Book your stay at ' . $site->name) }}">
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
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/hms.css') }}?v=10">
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
        <link rel="icon" type="image/png" href="{{ asset($site->favicon ?: 'frontend/assets/img/favicon.png') }}">

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


    </body>
</html>
