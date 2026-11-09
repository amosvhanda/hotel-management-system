@php
    $site = App\Models\SiteSetting::current();
    $cover = App\Models\Room::whereNotNull('image')->inRandomOrder()->value('image');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') | {{ $site->name }}</title>
    @include('partials.favicons', ['iconSite' => $site])
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('hms-tokens.css') }}?v=2">
    @include('partials.theme-vars')
    <style>
        :root {
            --forest: var(--hms-forest); --canopy: var(--hms-canopy); --leaf: var(--hms-leaf); --sand: var(--hms-sand);
            --paper: var(--hms-card); --ink: var(--hms-ink); --soft: var(--hms-muted); --line: var(--hms-line); --err: var(--hms-bad);
            --display: var(--hms-display); --text: var(--hms-text);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body { font-family: var(--text); color: var(--ink); background: var(--paper); -webkit-font-smoothing: antialiased; line-height: 1.55; }
        a { color: var(--canopy); text-underline-offset: 3px; }
        a:hover { color: var(--leaf); }

        .auth { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); }

        .auth-photo {
            position: relative; display: flex; flex-direction: column; justify-content: flex-end;
            padding: clamp(28px, 4vw, 56px); color: var(--hms-on-dark); background: var(--forest) center / cover no-repeat; overflow: hidden;
        }
        .auth-photo::before { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(14,26,19,.35) 0%, rgba(14,26,19,.12) 35%, rgba(14,26,19,.88) 100%); }
        .auth-photo > * { position: relative; }
        .auth-photo .brand { position: absolute; top: clamp(24px, 3vw, 40px); left: clamp(28px, 4vw, 56px); font-family: var(--display); font-size: 20px; color: var(--hms-on-dark); text-decoration: none; letter-spacing: -.005em; }
        .auth-photo h2 { font-family: var(--display); font-weight: 300; font-size: clamp(34px, 3.8vw, 56px); line-height: 1.04; letter-spacing: -.025em; margin: 0 0 14px; max-width: 14ch; }
        .auth-photo p { margin: 0; color: #d3dacd; max-width: 40ch; font-size: 16px; }

        .auth-form { display: flex; align-items: center; justify-content: center; padding: clamp(28px, 5vw, 64px); }
        .auth-card { width: 100%; max-width: 400px; }
        .auth-mobile-brand { display: none; font-family: var(--display); font-size: 20px; margin-bottom: 28px; color: var(--canopy); text-decoration: none; }
        .auth-card h1 { font-family: var(--display); font-weight: 400; font-size: clamp(32px, 3vw, 40px); line-height: 1.08; letter-spacing: -.02em; margin: 0 0 8px; }
        .auth-card .lead { color: var(--soft); margin: 0 0 28px; max-width: 46ch; }

        .field { margin-bottom: 18px; }
        .field label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; }
        .field input[type=text], .field input[type=email], .field input[type=password] {
            width: 100%; height: 50px; padding: 0 14px; font: inherit; color: var(--ink); background: #fff;
            border: 1px solid var(--line); border-radius: 6px; transition: border-color .15s ease, box-shadow .15s ease;
        }
        .field input:focus { outline: 0; border-color: var(--leaf); box-shadow: 0 0 0 3px rgba(63,107,74,.18); }
        .field input[aria-invalid=true] { border-color: #c98a7c; }
        .field .hint { color: var(--soft); font-size: 13px; margin-top: 6px; }
        .field .err { color: var(--err); font-size: 13.5px; margin-top: 6px; }
        .pw { position: relative; }
        .pw input { padding-right: 76px !important; }
        .pw button { position: absolute; right: 6px; top: 6px; height: 38px; padding: 0 12px; border: 0; background: transparent; color: var(--canopy); font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; border-radius: 4px; }
        .pw button:hover { background: var(--hms-soft); }

        .row-split { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: -4px 0 22px; font-size: 14.5px; }
        .check { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .check input { width: 18px; height: 18px; accent-color: var(--canopy); }

        .btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-height: 52px; padding: 0 22px; font: inherit; font-weight: 600; font-size: 16px; color: var(--hms-on-dark); background: var(--canopy); border: 0; border-radius: 6px; cursor: pointer; transition: background-color .2s ease; }
        .btn:hover { background: var(--leaf); }
        .btn:focus-visible, .pw button:focus-visible, a:focus-visible { outline: 2px solid var(--leaf); outline-offset: 2px; }
        .btn-quiet { background: transparent; color: var(--canopy); border: 1px solid var(--line); }
        .btn-quiet:hover { background: var(--hms-soft); }

        .alt { margin: 24px 0 0; color: var(--soft); font-size: 15px; }
        .notice { padding: 12px 14px; border-radius: 6px; margin-bottom: 20px; font-size: 14.5px; background: var(--hms-ok-bg); color: var(--hms-ok); border: 1px solid #cfe0c9; }
        .notice.bad { background: var(--hms-bad-bg); color: var(--err); border-color: #e8c7bf; }
        .back { display: inline-block; margin-top: 28px; font-size: 14px; color: var(--soft); }

        @media (max-width: 900px) {
            .auth { grid-template-columns: 1fr; }
            .auth-photo { display: none; }
            .auth-form { align-items: flex-start; padding-top: 40px; }
            .auth-mobile-brand { display: inline-block; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body>
<div class="auth">
    <aside class="auth-photo" @if($cover) style="background-image:url('{{ url('upload/roomimg/' . $cover) }}')" @endif>
        <a class="brand" href="{{ route('home') }}">@if ($site->logo_light_url)<img src="{{ $site->logo_light_url }}" alt="{{ $site->name }}" style="max-height:40px;display:block">@else{{ $site->name }}@endif</a>
        <h2>@yield('side_title', 'Your room is waiting.')</h2>
        <p>@yield('side_text', 'Sign in to see your stays, change dates and settle payments in one place.')</p>
    </aside>

    <main class="auth-form">
        <div class="auth-card">
            <a class="auth-mobile-brand" href="{{ route('home') }}">@if ($site->logo_url)<img src="{{ $site->logo_url }}" alt="{{ $site->name }}" style="max-height:40px;display:block">@else{{ $site->name }}@endif</a>
            @if (session('status') && session('status') !== 'verification-link-sent')
                <div class="notice" role="status">{{ session('status') }}</div>
            @endif
            @yield('content')
            <a class="back" href="{{ route('home') }}">Back to {{ $site->name }}</a>
        </div>
    </main>
</div>
<script>
    document.querySelectorAll('.pw button').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = b.parentNode.querySelector('input'), show = i.type === 'password';
            i.type = show ? 'text' : 'password';
            b.textContent = show ? 'Hide' : 'Show';
            b.setAttribute('aria-pressed', show);
        });
    });
</script>
</body>
</html>
