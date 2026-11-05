@php
    $b = $booking;
    $money = fn ($v) => '$' . number_format((float) $v, 2);
    $date = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('D, d M Y') : '—';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f4f1ee;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1ee;padding:30px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;">
                <tr>
                    <td style="background:#1f2937;padding:26px 32px;color:#ffffff;">
                        <div style="font-size:22px;font-weight:bold;">{{ $site->name }}</div>
                        @if ($site->tagline)<div style="font-size:13px;color:#d1d5db;margin-top:4px;">{{ $site->tagline }}</div>@endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px 32px 10px;">
                        <div style="display:inline-block;background:#dcfce7;color:#166534;font-size:12px;font-weight:bold;padding:5px 12px;border-radius:999px;">BOOKING CONFIRMED</div>
                        <h1 style="font-size:22px;margin:16px 0 8px;">Hello {{ $b['name'] ?? 'Guest' }},</h1>
                        <p style="margin:0 0 12px;line-height:1.6;color:#4b5563;">
                            Thank you for choosing {{ $site->name }}. Your booking is confirmed and we look forward to welcoming you.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:10px;">
                            @foreach (array_filter([
                                'Booking number' => isset($b['code']) ? '#' . $b['code'] : null,
                                'Room'           => $b['room'] ?? null,
                                'Check-in'       => $date($b['check_in'] ?? null),
                                'Check-out'      => $date($b['check_out'] ?? null),
                                'Nights'         => $b['nights'] ?? null,
                                'Rooms'          => $b['rooms'] ?? null,
                                'Guests'         => $b['guests'] ?? null,
                                'Total'          => isset($b['total']) ? $money($b['total']) : null,
                                'Payment'        => $b['payment'] ?? null,
                            ], fn ($v) => $v !== null && $v !== '') as $label => $value)
                                <tr>
                                    <td style="padding:11px 16px;border-bottom:1px solid #f1f5f9;color:#6b7280;font-size:14px;">{{ $label }}</td>
                                    <td style="padding:11px 16px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:bold;font-size:14px;">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 32px 8px;">
                        <p style="margin:0 0 12px;line-height:1.6;color:#4b5563;">
                            You can view your booking and download your invoice at any time from <strong>My Bookings</strong>.
                        </p>
                        <a href="{{ route('user.booking') }}" style="display:inline-block;background:#b56952;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:bold;">View my booking</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 32px 30px;color:#6b7280;font-size:13px;line-height:1.6;">
                        Need to change something? Reply to this email
                        @if ($site->phone) or call us on <strong>{{ $site->phone }}</strong>@endif
                        and quote your booking number.
                    </td>
                </tr>
                <tr>
                    <td style="background:#f9fafb;padding:18px 32px;color:#9ca3af;font-size:12px;line-height:1.6;">
                        {{ $site->name }}@if ($site->address) · {{ $site->address }}@endif
                        @if ($site->email)<br>{{ $site->email }}@endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
