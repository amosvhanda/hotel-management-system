@php
    $site = App\Models\SiteSetting::current();
    $money = fn ($v) => '$' . number_format((float) $v, 2);
    $logo = $site->logo && is_file(public_path($site->logo)) ? public_path($site->logo) : public_path(App\Models\SiteSetting::DEFAULT_LOGO);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $editData->code }}</title>
    <style>
        * { font-family: DejaVu Sans, Arial, sans-serif; }
        body { color: #2d3748; font-size: 12px; margin: 0; }
        .wrap { padding: 28px 34px; }
        .top { width: 100%; border-bottom: 3px solid #b56952; padding-bottom: 14px; }
        .top td { vertical-align: top; }
        .brand { font-size: 22px; font-weight: bold; color: #1a202c; }
        .muted { color: #718096; }
        .title { font-size: 26px; font-weight: bold; color: #b56952; text-align: right; letter-spacing: 1px; }
        .meta { text-align: right; line-height: 1.7; }
        .grid { width: 100%; margin-top: 22px; }
        .grid td { vertical-align: top; width: 50%; }
        h4 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #718096; margin: 0 0 6px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.items th { background: #1a202c; color: #fff; text-align: left; padding: 9px 8px; font-size: 11px; }
        table.items td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; }
        .r { text-align: right; }
        table.totals { width: 45%; margin-left: 55%; border-collapse: collapse; margin-top: 12px; }
        table.totals td { padding: 7px 8px; }
        table.totals tr.grand td { border-top: 2px solid #1a202c; font-size: 15px; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 9px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .paid { background: #c6f6d5; color: #22543d; }
        .unpaid { background: #fed7d7; color: #822727; }
        .foot { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 12px; text-align: center; color: #718096; font-size: 11px; }
    </style>
</head>
<body>
<div class="wrap">
    <table class="top">
        <tr>
            <td>
                @if ($logo)
                    <img src="{{ $logo }}" style="height:44px"><br>
                @else
                    <div class="brand">{{ $site->name }}</div>
                @endif
                <div class="muted" style="margin-top:6px; line-height:1.6">
                    @if ($site->address){{ $site->address }}<br>@endif
                    @if ($site->phone)Tel: {{ $site->phone }}<br>@endif
                    @if ($site->email){{ $site->email }}@endif
                </div>
            </td>
            <td class="meta">
                <div class="title">INVOICE</div>
                <strong>Booking #{{ $editData->code }}</strong><br>
                Date: {{ \Carbon\Carbon::parse($editData->created_at)->format('d M Y') }}<br>
                Payment: {{ $editData->payment_method === 'COD' ? 'Cash on arrival' : $editData->payment_method }}<br>
                <span class="badge {{ $editData->payment_status == 1 ? 'paid' : 'unpaid' }}">
                    {{ $editData->payment_status == 1 ? 'PAID' : 'UNPAID' }}
                </span>
            </td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td>
                <h4>Billed to</h4>
                <strong>{{ $editData->name }}</strong><br>
                {{ $editData->email }}<br>
                {{ $editData->phone }}<br>
                {{ collect([$editData->address, $editData->state, $editData->country])->filter()->implode(', ') }}
            </td>
            <td>
                <h4>Stay</h4>
                Check-in: <strong>{{ \Carbon\Carbon::parse($editData->check_in)->format('D, d M Y') }}</strong><br>
                Check-out: <strong>{{ \Carbon\Carbon::parse($editData->check_out)->format('D, d M Y') }}</strong><br>
                Guests: {{ $editData->persion }}
                @if ($editData->transation_id)<br>Payment ref: {{ $editData->transation_id }}@endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Room</th>
                <th class="r">Rooms</th>
                <th class="r">Nights</th>
                <th class="r">Rate / night</th>
                <th class="r">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $editData->room->type->name ?? 'Room' }}</td>
                <td class="r">{{ $editData->number_of_rooms }}</td>
                <td class="r">{{ $editData->total_night }}</td>
                <td class="r">{{ $money($editData->actual_price) }}</td>
                <td class="r">{{ $money($editData->subtotal) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="r">{{ $money($editData->subtotal) }}</td></tr>
        @if ((float) $editData->discount > 0)
            <tr><td>Discount</td><td class="r">- {{ $money($editData->discount) }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td class="r">{{ $money($editData->total_price) }}</td></tr>
    </table>

    <div class="foot">
        Thank you for staying with {{ $site->name }}. Please bring this invoice when you check in.
    </div>
</div>
</body>
</html>
