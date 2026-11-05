@php
    $site = App\Models\SiteSetting::current();
    $cover = App\Models\Room::whereNotNull('image')->latest()->value('image');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset($site->favicon ?: 'backend/assets/images/favicon-32x32.png') }}" type="image/png" />
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght,SOFT@9..144,300;9..144,400&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/hms-admin.css') }}?v=1" rel="stylesheet">
	<link href="{{ asset('backend/assets/css/hms-admin-premium.css') }}?v=1" rel="stylesheet" />
    <title>Staff Sign In | {{ $site->name }}</title>
</head>

<body>
    <div class="hms-auth">
        <div class="hms-auth-side" style="background-image:url('{{ $cover ? url('upload/roomimg/' . $cover) : '' }}')">
            <span class="badge bg-light text-dark mb-3 align-self-start">Staff portal</span>
            <h1>{{ $site->name }}</h1>
            <p>Manage bookings, rooms, guests and payments in one place.</p>
        </div>

        <div class="hms-auth-form">
            <div class="inner">
                <div class="mb-4">
                    @if ($site->logo)
                        <img src="{{ asset($site->logo) }}" alt="{{ $site->name }}" style="max-height:44px" class="mb-3">
                    @endif
                    <h3 class="fw-bold mb-1">Welcome back</h3>
                    <p class="text-secondary mb-0">Sign in with your staff account.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <label for="login" class="form-label">Email, username or phone</label>
                        <input type="text" name="login" id="login" value="{{ old('login') }}" autofocus autocomplete="username"
                               class="form-control @error('login') is-invalid @enderror" placeholder="admin@example.com">
                        @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group" id="show_hide_password">
                            <input type="password" name="password" id="password" autocomplete="current-password"
                                   class="form-control border-end-0 @error('password') is-invalid @enderror" placeholder="Enter password">
                            <a href="javascript:;" class="input-group-text bg-transparent" aria-label="Show password"><i class="bx bx-hide"></i></a>
                        </div>
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bx bx-lock-open-alt"></i> Sign in</button>
                    </div>
                </form>

                <p class="text-secondary small mt-4 mb-0">
                    <a href="{{ route('home') }}"><i class="bx bx-arrow-back"></i> Back to website</a>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
    <script>
        $('#show_hide_password a').on('click', function (e) {
            e.preventDefault();
            var input = $('#show_hide_password input');
            var icon = $('#show_hide_password i');
            var show = input.attr('type') === 'password';
            input.attr('type', show ? 'text' : 'password');
            icon.toggleClass('bx-hide', !show).toggleClass('bx-show', show);
        });
    </script>
</body>
</html>
