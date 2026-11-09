@extends('frontend.main_master')
@section('title', 'Checkout')
@section('main')

@php
    $subtotal = $room->price * $nights * $book_data['number_of_rooms'];
    $discount = round(($room->discount / 100) * $subtotal, 2);
    $total = $subtotal - $discount;
    $money = fn ($v) => '$' . number_format((float) $v, 2);
    $user = Auth::user();
    $countries = ['Zimbabwe', 'South Africa', 'Botswana', 'Zambia', 'Mozambique', 'Namibia', 'Malawi', 'Other'];
    $provinces = ['Harare', 'Bulawayo', 'Manicaland', 'Mashonaland Central', 'Mashonaland East', 'Mashonaland West', 'Masvingo', 'Matabeleland North', 'Matabeleland South', 'Midlands'];
    $ecocashReady = app(\App\Services\PaynowGateway::class)->configured();
    $method = $ecocashReady ? old('payment_method', 'ecocash') : 'COD';
@endphp

@include('frontend.body.page_head', ['title' => 'Complete your booking', 'class' => 'hms-page-head--booking'])

<section class="checkout-area pt-100 pb-70">
    <div class="container">
        <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm" novalidate>
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="hms-card">
                        <h3>1. Guest details</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Full name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', $user->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email', $user->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone">Phone <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" required placeholder="0771234567" value="{{ old('phone', $user->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country">Country <span class="required">*</span></label>
                                <input type="text" id="country" name="country" list="countryList" class="form-control @error('country') is-invalid @enderror" required value="{{ old('country', 'Zimbabwe') }}">
                                <datalist id="countryList">
                                    @foreach ($countries as $c)<option value="{{ $c }}">@endforeach
                                </datalist>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="address">Address <span class="required">*</span></label>
                                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" required value="{{ old('address', $user->address) }}">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state">Province / State <span class="required">*</span></label>
                                <input type="text" id="state" name="state" list="provinceList" class="form-control @error('state') is-invalid @enderror" required value="{{ old('state', 'Harare') }}">
                                <datalist id="provinceList">
                                    @foreach ($provinces as $p)<option value="{{ $p }}">@endforeach
                                </datalist>
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="hms-card">
                        <h3>2. Payment</h3>

                        @if ($ecocashReady)
                        <label class="hms-pay-option {{ $method === 'paynow' ? 'is-active' : '' }}">
                            <input type="radio" name="payment_method" value="paynow" @checked($method === 'paynow')>
                            <div>
                                <strong>Paynow <small class="text-muted fw-normal">pay now</small></strong>
                                <span>Pay on Paynow's secure page — Visa/Mastercard, ZIPIT and other Paynow options.</span>
                            </div>
                        </label>

                        <label class="hms-pay-option {{ $method === 'ecocash' ? 'is-active' : '' }}">
                            <input type="radio" name="payment_method" value="ecocash" @checked($method === 'ecocash')>
                            <div>
                                <strong>EcoCash <small class="text-muted fw-normal">pay now</small></strong>
                                <span>Pay from your EcoCash wallet (077 / 078). You approve it on your phone with your PIN.</span>
                            </div>
                        </label>
                        @else
                        @foreach (['Paynow' => 'Card and other Paynow payments', 'EcoCash' => 'EcoCash wallet payments'] as $label => $what)
                        <label class="hms-pay-option is-disabled" aria-disabled="true" style="opacity:.55;cursor:not-allowed">
                            <input type="radio" name="payment_method" disabled>
                            <div>
                                <strong>{{ $label }} <small class="text-muted fw-normal">coming soon</small></strong>
                                <span>{{ $what }} aren't switched on yet. Please choose cash for now.</span>
                            </div>
                        </label>
                        @endforeach
                        @endif

                        <label class="hms-pay-option {{ $method === 'COD' ? 'is-active' : '' }}">
                            <input type="radio" name="payment_method" value="COD" @checked($method === 'COD')>
                            <div>
                                <strong>Cash <small class="text-muted fw-normal">pay on arrival</small></strong>
                                <span>Reserve now and pay in cash when you arrive at the hotel.</span>
                            </div>
                        </label>

                        @if ($ecocashReady)
                        <div id="paynowSection" class="mt-3 {{ $method === 'ecocash' ? '' : 'd-none' }}">
                            <label for="paynow_phone">EcoCash number <span class="required">*</span></label>
                            <input type="tel" id="paynow_phone" name="paynow_phone" class="form-control @error('paynow_phone') is-invalid @enderror" placeholder="e.g. 0771234567" value="{{ old('paynow_phone', $user->phone) }}">
                            @error('paynow_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">You'll get a prompt on this phone — enter your EcoCash PIN to approve.</small>
                        </div>
                        @endif

                        <button type="submit" class="default-btn btn-bg-one border-radius-5 w-100 mt-4" id="placeOrder">
                            Confirm Booking · {{ $money($total) }}
                        </button>
                        <p class="hms-secure text-center"><i class="bx bx-lock-alt"></i> Your details are only used for this reservation.</p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="hms-card" style="position: sticky; top: 100px;">
                        <h3>Booking summary</h3>
                        <div class="hms-summary-room">
                            <img src="{{ $room->image ? url('upload/roomimg/' . $room->image) : url('upload/no_image.jpg') }}" alt="{{ $room->type->name ?? 'Room' }}">
                            <div>
                                <h4>{{ $room->type->name ?? 'Room' }}</h4>
                                <p>{{ $money($room->price) }} / night</p>
                            </div>
                        </div>

                        <div class="hms-dates">
                            <div><small>Check-in</small><strong>{{ \Carbon\Carbon::parse($book_data['check_in'])->format('D, d M Y') }}</strong></div>
                            <div><small>Check-out</small><strong>{{ \Carbon\Carbon::parse($book_data['check_out'])->format('D, d M Y') }}</strong></div>
                        </div>

                        <table class="hms-lines">
                            <tr><td>{{ $nights }} night(s) × {{ $book_data['number_of_rooms'] }} room(s)</td><td>{{ $money($subtotal) }}</td></tr>
                            <tr><td>Guests</td><td>{{ $book_data['persion'] }}</td></tr>
                            @if ($discount > 0)
                                <tr><td>Discount ({{ $room->discount }}%)</td><td class="text-success">- {{ $money($discount) }}</td></tr>
                            @endif
                            <tr class="total"><td>Total</td><td>{{ $money($total) }}</td></tr>
                        </table>

                        <a href="{{ route('search_room_details', $room->id) }}?check_in={{ $book_data['check_in'] }}&check_out={{ $book_data['check_out'] }}&persion={{ $book_data['persion'] }}" class="d-inline-block mt-3">
                            <i class="bx bx-edit"></i> Change dates or rooms
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
    (function () {
        var options = document.querySelectorAll('.hms-pay-option');
        var section = document.getElementById('paynowSection');
        options.forEach(function (opt) {
            opt.querySelector('input').addEventListener('change', function () {
                options.forEach(function (o) { o.classList.remove('is-active'); });
                opt.classList.add('is-active');
                if (!section) return; // EcoCash not switched on yet
                section.classList.toggle('d-none', this.value !== 'ecocash');
            });
        });
        document.getElementById('checkoutForm').addEventListener('submit', function () {
            var btn = document.getElementById('placeOrder');
            btn.disabled = true;
            btn.textContent = 'Processing…';
        });
    })();
</script>
@endpush
@endsection
