@php($site = App\Models\SiteSetting::current())
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;background:#f4f3ec;font-family:Arial,Helvetica,sans-serif;color:#15201a">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f3ec;padding:24px 12px">
  <tr><td align="center">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:10px;overflow:hidden">
      <tr><td style="padding:22px 28px;border-bottom:3px solid #c9b88a">
        <img src="{{ $site->logo_url }}" alt="{{ $site->name }}" style="max-height:44px;display:block">
      </td></tr>
      <tr><td style="padding:26px 28px;font-size:15px;line-height:1.6">
        @foreach (preg_split("/\n{2,}/", trim($bodyText)) as $para)
          <p style="margin:0 0 14px">{!! nl2br(e($para)) !!}</p>
        @endforeach
        @if ($buttonText && $buttonUrl)
          <p style="margin:22px 0 6px">
            <a href="{{ $buttonUrl }}" style="background:#1d3a2a;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:6px;display:inline-block;font-weight:bold">{{ $buttonText }}</a>
          </p>
        @endif
      </td></tr>
      <tr><td style="padding:16px 28px;background:#fbfbf6;font-size:12px;color:#5d695f;line-height:1.5">
        {{ $site->name }} · {{ $site->address }}<br>
        {{ $site->phone }}@if ($site->email) · <a href="mailto:{{ $site->email }}" style="color:#5d695f">{{ $site->email }}</a>@endif
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>
