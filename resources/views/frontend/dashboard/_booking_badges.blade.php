@if ((int) $item->status === 1)
    <span class="hms-badge ok">Confirmed</span>
@else
    <span class="hms-badge wait">Pending</span>
@endif
@if ((int) $item->payment_status === 1)
    <span class="hms-badge ok">Paid</span>
@elseif (in_array(strtolower((string) $item->paynow_status), ['cancelled', 'failed']))
    <span class="hms-badge bad">Payment {{ strtolower($item->paynow_status) }}</span>
@else
    <span class="hms-badge wait">{{ $item->payment_method === 'COD' ? 'Pay at hotel' : 'Awaiting payment' }}</span>
@endif
