@extends('frontend.main_master')
@section('title', 'Available rooms')
@section('main')

@php
    $site = App\Models\SiteSetting::current();
@endphp
@include('frontend.body.page_head', [
    'kicker' => $site->home('rooms_page_kicker'),
    'title' => $site->home('rooms_page_title'),
    'lead' => \Carbon\Carbon::parse($checkIn)->format('d M').' to '.\Carbon\Carbon::parse($checkOut)->format('d M Y').', '.$nights.($nights === 1 ? ' night' : ' nights').', '.$guests.($guests === 1 ? ' guest' : ' guests'),
])

<section class="hms-band hms-band--paper">
    <div class="container">
        @include('frontend.room._filters', [
            'action' => route('booking.search'),
            'resetUrl' => route('booking.search', ['check_in' => $checkIn, 'check_out' => $checkOut, 'persion' => 1]),
            'showDates' => false,
            'count' => $rooms->count(),
        ])

        <div class="hms-room-grid">
            @forelse ($rooms as $item)
                @include('frontend.room._tile', [
                    'room' => $item,
                    'url' => route('search_room_details', $item->id).'?'.$stayQuery,
                    'left' => $item->rooms_left,
                ])
            @empty
                <div class="hms-empty">
                    No rooms are free for those dates.
                    <div class="mt-3"><a href="{{ route('froom.all') }}" class="hms-text-link">Try different dates</a></div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
