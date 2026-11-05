@extends('frontend.main_master')
@section('title', 'Payment Status')
@section('main')

@php
    $paid = (int) $booking->payment_status === 1;
    $failed = ! $paid && in_array(strtolower((string) $booking->paynow_status), ['cancelled', 'failed'], true);
    $wallet = $booking->payment_method === 'OneMoney' ? 'NetOne OneMoney' : 'EcoCash';
@endphp

@include('frontend.body.page_head', ['title' => 'Booking #'.$booking->code, 'class' => 'hms-page-head--booking'])

<section class="pt-100 pb-100">
    <div class="container">
        <div class="hms-card hms-status" id="paymentStatus"
             data-url="{{ route('paynow.return', $booking->id) }}" data-done="{{ $paid || $failed ? 1 : 0 }}">

            @if ($paid)
                <div class="icon ok"><i class="bx bx-check"></i></div>
                <h2>Payment received — you're booked!</h2>
                <p class="text-muted">We've received {{ \App\Models\SiteSetting::current()->formatMoney($booking->total_price, 2) }} via {{ $wallet }}. A confirmation will follow once the hotel confirms your room.</p>
            @elseif ($failed)
                <div class="icon bad"><i class="bx bx-x"></i></div>
                <h2>Payment {{ strtolower($booking->paynow_status) }}</h2>
                <p class="text-muted">No money was taken and the room has been released. You can book again and choose another payment method.</p>
            @else
                <div class="icon wait"><i class="bx bx-mobile-alt"></i></div>
                <h2>Approve the payment on your phone</h2>
                <p class="text-muted">
                    A {{ $wallet }} prompt for <strong>{{ \App\Models\SiteSetting::current()->formatMoney($booking->total_price, 2) }}</strong> has been sent to your phone.
                    Enter your PIN to complete the booking.
                </p>
                <p><span class="hms-spinner"></span> <span id="statusText">Waiting for confirmation…</span></p>
            @endif

            <div class="hms-dates mt-4 text-start">
                <div><small>Room</small><strong>{{ $booking->room->type->name ?? 'Room' }}</strong></div>
                <div><small>Check-in</small><strong>{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</strong></div>
                <div><small>Check-out</small><strong>{{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</strong></div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                @if ($failed)
                    <a href="{{ route('froom.all') }}" class="default-btn btn-bg-one border-radius-5">Book Again</a>
                @else
                    <a href="{{ route('user.booking') }}" class="default-btn btn-bg-one border-radius-5">View My Bookings</a>
                    @if ($paid)
                        <a href="{{ route('user.invoice', $booking->id) }}" class="btn btn-outline-dark px-4"><i class="bx bx-download"></i> Invoice</a>
                    @else
                        <a href="{{ route('paynow.return', $booking->id) }}" class="btn btn-outline-dark px-4">Check again</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function () {
        var box = document.getElementById('paymentStatus');
        if (!box || box.dataset.done === '1') return;
        var tries = 0;
        var timer = setInterval(function () {
            tries++;
            fetch(box.dataset.url, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    var s = (data.status || '').toLowerCase();
                    if (data.paid || s === 'cancelled' || s === 'failed') {
                        clearInterval(timer);
                        window.location.reload();
                    } else if (data.status) {
                        document.getElementById('statusText').textContent = 'Status: ' + data.status + ' — waiting for your approval…';
                    }
                })
                .catch(function () {});
            if (tries >= 40) { // ~3 minutes
                clearInterval(timer);
                document.getElementById('statusText').textContent = 'Still waiting. If you approved the payment, tap "Check again".';
            }
        }, 5000);
    })();
</script>
@endpush
@endsection
