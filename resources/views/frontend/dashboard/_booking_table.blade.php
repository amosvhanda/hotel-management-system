@if ($bookings->isEmpty())
    <div class="hms-empty">
        <i class="bx bx-calendar-x"></i>
        You have no bookings yet.
        <div class="mt-3"><a href="{{ route('froom.all') }}" class="default-btn btn-bg-one border-radius-5">Find a Room</a></div>
    </div>
@else
    <div class="table-responsive">
        <table class="hms-table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Room</th>
                    <th>Stay</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $item)
                    <tr>
                        <td><strong>#{{ $item->code }}</strong><br><small class="text-muted">{{ $item->created_at?->format('d M Y') }}</small></td>
                        <td>{{ $item->room->type->name ?? 'Room' }}<br><small class="text-muted">{{ $item->number_of_rooms }} room(s) · {{ $item->persion }} guest(s)</small></td>
                        <td>{{ \Carbon\Carbon::parse($item->check_in)->format('d M') }} – {{ \Carbon\Carbon::parse($item->check_out)->format('d M Y') }}<br><small class="text-muted">{{ $item->total_night }} night(s)</small></td>
                        <td><strong>{{ \App\Models\SiteSetting::current()->formatMoney($item->total_price, 2) }}</strong></td>
                        <td>@include('frontend.dashboard._booking_badges')</td>
                        <td class="text-end text-nowrap">
                            @if ($item->paynow_poll_url && (int) $item->payment_status !== 1 && ! in_array(strtolower((string) $item->paynow_status), ['cancelled', 'failed']))
                                <a href="{{ route('paynow.return', $item->id) }}" class="btn btn-sm btn-outline-secondary">Check payment</a>
                            @endif
                            <a href="{{ route('user.invoice', $item->id) }}" class="btn btn-sm btn-outline-dark" title="Download invoice"><i class="bx bx-download"></i> Invoice</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
