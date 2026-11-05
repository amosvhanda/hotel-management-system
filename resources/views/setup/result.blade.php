<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup</title>
    <style>
        body { font-family: system-ui, "Segoe UI", Arial, sans-serif; background: #f6f4f2; color: #1f2937; margin: 0; padding: 40px 16px; }
        .box { max-width: 760px; margin: 0 auto; background: #fff; border-radius: 14px; padding: 28px 32px; box-shadow: 0 10px 30px rgba(0,0,0,.07); }
        h1 { margin: 0 0 6px; font-size: 24px; } .ok { color: #15803d; } .bad { color: #b91c1c; }
        pre { background: #111827; color: #e5e7eb; padding: 14px; border-radius: 8px; white-space: pre-wrap; font-size: 13px; }
        a.btn { display: inline-block; margin: 8px 8px 0 0; background: #b56952; color: #fff; padding: 10px 18px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
<div class="box">
    @if ($ok)
        <h1 class="ok">✔ Your hotel system is ready</h1>
        <p>The database is up to date. This setup page is now switched off.</p>
    @else
        <h1 class="bad">Setup hit a problem</h1>
        <p>Copy the message below and send it to Claude. You can open this page again after it's fixed.</p>
    @endif

    @foreach ($steps as [$cmd, $good, $out])
        <p><strong>{{ $cmd }}</strong> — <span class="{{ $good ? 'ok' : 'bad' }}">{{ $good ? 'done' : 'failed' }}</span></p>
        @if ($out !== '')<pre>{{ $out }}</pre>@endif
    @endforeach

    <p>
        <a class="btn" href="{{ url('/') }}">Open website</a>
        <a class="btn" href="{{ url('/admin/login') }}">Open admin panel</a>
    </p>
</div>
</body>
</html>
