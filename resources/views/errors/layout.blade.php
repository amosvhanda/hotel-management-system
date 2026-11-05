@php
    // Must never fail: if the database is down we still show a friendly page.
    $siteName = rescue(fn () => App\Models\SiteSetting::current()->name, config('app.name', 'Hotel'), false);
    $logo = rescue(fn () => App\Models\SiteSetting::current()->logo_url, null, false);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') · @yield('title') | {{ $siteName }}</title>
    <style>
        *{box-sizing:border-box} body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f6f4f2;color:#1f2937;font-family:system-ui,"Segoe UI",Arial,sans-serif;padding:24px}
        .card{max-width:560px;width:100%;background:#fff;border-radius:16px;padding:40px 36px;text-align:center;box-shadow:0 14px 40px rgba(17,24,39,.08)}
        .logo{max-height:46px;margin-bottom:22px} .brand{font-weight:700;font-size:20px;margin-bottom:22px}
        .code{font-size:64px;font-weight:800;line-height:1;color:#b56952;letter-spacing:-2px}
        h1{font-size:24px;margin:14px 0 8px} p{color:#6b7280;line-height:1.6;margin:0 0 24px}
        .btns{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}
        a.btn{display:inline-block;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:600}
        a.primary{background:#b56952;color:#fff} a.ghost{background:#f3f4f6;color:#1f2937}
    </style>
</head>
<body>
    <div class="card">
        @if ($logo)<img class="logo" src="{{ $logo }}" alt="{{ $siteName }}">@else<div class="brand">{{ $siteName }}</div>@endif
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <div class="btns">
            <a class="btn primary" href="{{ url('/') }}">Go to homepage</a>
            @yield('extra')
        </div>
    </div>
</body>
</html>
