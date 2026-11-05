@extends('frontend.main_master')
@section('main')

@php($site = App\Models\SiteSetting::current())
@include('frontend.body.page_head', [
    'kicker' => $site->home('rooms_page_kicker'),
    'title' => $site->home('rooms_page_title'),
    'lead' => $site->home('booking_note'),
])

<section class="hms-band hms-band--paper">
    <div class="container">
        @php $empty_array = []; @endphp
        <div class="hms-room-grid">
            @foreach ($rooms as $item)
                @php
                    $av_room = App\Models\Booking::availableRooms($item->id, old('check_in'), old('check_out'));
                @endphp
                @if ($av_room > 0 && old('persion') <= $item->total_adult)
                    @include('frontend.room._tile', [
                        'room' => $item,
                        'url' => route('search_room_details', $item->id.'?check_in='.old('check_in').'&check_out='.old('check_out').'&persion='.old('persion')),
                    ])
                @else
                    @php $empty_array[] = $item->id; @endphp
                @endif
            @endforeach
        </div>
        @if (count($rooms) == count($empty_array))
            <p class="hms-empty">No rooms are free for those dates.</p>
        @endif
    </div>
</section>

@endsection
